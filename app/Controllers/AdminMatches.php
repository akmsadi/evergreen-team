<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\MatchBallModel;
use App\Libraries\MatchFinanceService;
use App\Models\MatchExpenseModel;
use App\Models\MatchInningsModel;
use App\Models\MatchModel;
use App\Models\MatchPlayerModel;
use App\Models\PlayerModel;
use App\Models\VenueModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminMatches extends BaseController
{
    private AuditLogger $auditLogger;
    private MatchBallModel $matchBalls;
    private MatchModel $matches;
    private MatchExpenseModel $matchExpenses;
    private MatchFinanceService $matchFinance;
    private MatchInningsModel $matchInnings;
    private MatchPlayerModel $matchPlayers;
    private PlayerModel $players;
    private VenueModel $venues;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->auditLogger = new AuditLogger();
        $this->matchBalls = new MatchBallModel();
        $this->matches = new MatchModel();
        $this->matchExpenses = new MatchExpenseModel();
        $this->matchFinance = new MatchFinanceService();
        $this->matchInnings = new MatchInningsModel();
        $this->matchPlayers = new MatchPlayerModel();
        $this->players = new PlayerModel();
        $this->venues = new VenueModel();
    }

    public function index(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/matches/index', [
            'username' => session()->get('admin_username'),
            'matches'  => $this->matches->orderedList(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/matches/create', $this->buildMatchFormViewData());
    }

    public function edit(int $matchId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        return view('admin/matches/create', $this->buildMatchFormViewData($match));
    }

    public function store()
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $validationError = $this->validateAndCollectMatchPayload();

        if ($validationError !== null) {
            return $validationError;
        }

        $matchData = $this->buildMatchDataFromRequest();
        $matchId = $this->matches->insert($matchData, true);

        $this->syncMatchPlayers(
            $matchId,
            $this->collectTeamPlayers('team_a_player_ids'),
            $this->collectTeamPlayers('team_b_player_ids')
        );

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Match created successfully. You can now add expenses and review player balances.');
    }

    public function update(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        if (($match['match_status'] ?? '') === 'completed') {
            return redirect()->to('/admin/matches/' . $matchId)
                ->with('error', 'Completed match fields are read-only. Finance, contributors, expenses, and scoreboard actions remain available.');
        }

        $validationError = $this->validateAndCollectMatchPayload($matchId);

        if ($validationError !== null) {
            return $validationError;
        }

        $teamAPlayers = $this->collectTeamPlayers('team_a_player_ids');
        $teamBPlayers = $this->collectTeamPlayers('team_b_player_ids');
        $selectedPlayers = array_values(array_unique(array_merge($teamAPlayers, $teamBPlayers)));
        $lockedPlayers = $this->getLockedParticipantIds($matchId);

        if (array_diff($lockedPlayers, $selectedPlayers) !== []) {
            return redirect()->back()->withInput()->with('errors', [
                'team_a_player_ids' => 'Players already used in match expenses must remain in the match squads.',
            ]);
        }

        $this->matches->update($matchId, $this->buildMatchDataFromRequest($match));
        $this->syncMatchPlayers($matchId, $teamAPlayers, $teamBPlayers);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Match updated successfully.');
    }

    public function delete(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->find($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        if (($match['match_status'] ?? '') !== 'scheduled') {
            return redirect()->to('/admin/matches')->with('error', 'Only scheduled matches can be archived.');
        }

        $this->matches->update($matchId, [
            'match_status' => 'archived',
        ]);

        return redirect()->to('/admin/matches')->with('success', 'Match archived successfully.');
    }

    public function show(int $matchId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $participants = $this->matchFinance->getParticipants($matchId);

        return view('admin/matches/show', [
            'username'         => session()->get('admin_username'),
            'match'            => $match,
            'participants'     => $participants,
            'matchContributorIds' => $this->matchFinance->getMatchContributorIds($matchId),
            'matchContributors' => $this->matchFinance->getMatchContributors($matchId),
            'scoreboard'       => $this->buildScoreboardData($match, $participants),
            'contributorGroups' => $this->matchFinance->buildContributorGroups($participants, $match),
            'expenses'         => $this->matchFinance->getExpenses($matchId),
            'financeSummary'   => $this->matchFinance->buildSummary($matchId, $participants),
        ]);
    }

    public function scoreboard(int $matchId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        return redirect()->to('/admin/matches/' . $matchId);
    }

    public function updateContributors(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $participantIds = $this->matchFinance->getParticipantIds($matchId);
        $contributorIds = array_map('intval', (array) $this->request->getPost('match_contributor_ids'));
        $contributorIds = array_values(array_unique(array_filter($contributorIds)));

        if ($contributorIds === []) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('match_contributor_errors', ['match_contributor_ids' => 'Select at least one match contributor.']);
        }

        if (array_diff($contributorIds, $participantIds) !== []) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('match_contributor_errors', ['match_contributor_ids' => 'Contributors must be participants in this match.']);
        }

        $this->matchFinance->syncMatchContributors($matchId, $contributorIds);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Match contributors updated successfully.');
    }

    public function start(int $matchId): ResponseInterface|string
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $participants = $this->matchFinance->getParticipants($matchId);
        $scoreboard = $this->buildScoreboardData($match, $participants);

        if (($match['match_status'] ?? '') !== 'live' && $scoreboard['innings'] === []) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->with('error', 'Only live matches can be started from the wizard.');
        }

        $completionPayload = $this->syncMatchCompletionState($match, $participants, ($match['match_status'] ?? '') !== 'completed');

        if ($completionPayload !== null) {
            return redirect()->to($completionPayload['redirectUrl'])->with('success', $completionPayload['message']);
        }

        return view('admin/matches/start', $this->buildMatchStartViewData($match, $participants, $scoreboard));
    }

    public function storeStart(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        if (($match['match_status'] ?? '') !== 'live') {
            return redirect()->to('/admin/matches/' . $matchId)
                ->with('error', 'Only live matches can be started from the wizard.');
        }

        $participants = $this->matchFinance->getParticipants($matchId);
        $scoreboard = $this->buildScoreboardData($match, $participants);

        if ($scoreboard['innings'] !== []) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => true,
                    'message' => 'The live scoreboard has already been started for this match.',
                    'step' => 3,
                    'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
                ]);
            }

            return redirect()->to('/admin/matches/' . $matchId . '/start')
                ->with('success', 'The live scoreboard has already been started for this match.');
        }

        $tossWinnerSide = (string) $this->request->getPost('toss_winner_side');
        $tossDecision = (string) $this->request->getPost('toss_decision');
        $strikerPlayerId = (int) $this->request->getPost('striker_player_id');
        $nonStrikerPlayerId = (int) $this->request->getPost('non_striker_player_id');
        $bowlerPlayerId = (int) $this->request->getPost('bowler_player_id');

        $sideLabels = [
            'team_a' => $this->getSideLabel('team_a', $match),
            'team_b' => $this->getSideLabel('team_b', $match),
        ];
        $oppositeSides = [
            'team_a' => 'team_b',
            'team_b' => 'team_a',
        ];
        $playerSides = [];

        foreach ($participants as $participant) {
            $playerSides[(int) $participant['player_id']] = (string) $participant['side'];
        }

        $errors = [];

        if (! isset($sideLabels[$tossWinnerSide])) {
            $errors['toss_winner_side'] = 'Select a valid toss winner.';
        }

        if (! in_array($tossDecision, ['bat', 'bowl'], true)) {
            $errors['toss_decision'] = 'Select a valid toss decision.';
        }

        $battingSide = isset($oppositeSides[$tossWinnerSide])
            ? ($tossDecision === 'bat' ? $tossWinnerSide : $oppositeSides[$tossWinnerSide])
            : null;
        $bowlingSide = $battingSide !== null ? $oppositeSides[$battingSide] : null;

        if (! isset($playerSides[$strikerPlayerId])) {
            $errors['striker_player_id'] = 'Select a valid opening batter.';
        }

        if (! isset($playerSides[$nonStrikerPlayerId])) {
            $errors['non_striker_player_id'] = 'Select a valid non-striker.';
        }

        if (! isset($playerSides[$bowlerPlayerId])) {
            $errors['bowler_player_id'] = 'Select a valid opening bowler.';
        }

        if ($strikerPlayerId > 0 && $strikerPlayerId === $nonStrikerPlayerId) {
            $errors['non_striker_player_id'] = 'Opening batters must be different players.';
        }

        if ($battingSide !== null && ($playerSides[$strikerPlayerId] ?? null) !== $battingSide) {
            $errors['striker_player_id'] = 'Opening batter must belong to the batting side.';
        }

        if ($battingSide !== null && ($playerSides[$nonStrikerPlayerId] ?? null) !== $battingSide) {
            $errors['non_striker_player_id'] = 'Non-striker must belong to the batting side.';
        }

        if ($bowlingSide !== null && ($playerSides[$bowlerPlayerId] ?? null) !== $bowlingSide) {
            $errors['bowler_player_id'] = 'Opening bowler must belong to the bowling side.';
        }

        if ($errors !== []) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => false,
                    'errors' => $errors,
                    'step' => $this->resolveStartWizardStep($errors),
                ], 422);
            }

            return redirect()->back()->withInput()->with('start_errors', $errors);
        }

        $this->matches->update($matchId, [
            'toss_winner' => $sideLabels[$tossWinnerSide],
            'toss_decision' => $tossDecision,
        ]);

        $this->matchInnings->insert([
            'match_id' => $matchId,
            'innings_number' => 1,
            'batting_side' => $battingSide,
            'bowling_side' => $bowlingSide,
            'runs' => 0,
            'wickets' => 0,
            'overs' => 0.0,
            'balls' => 0,
            'extras' => 0,
            'byes' => 0,
            'leg_byes' => 0,
            'wides' => 0,
            'no_balls' => 0,
            'target_runs' => null,
            'required_run_rate' => null,
            'completed' => 0,
            'opening_striker_player_id' => $strikerPlayerId,
            'opening_non_striker_player_id' => $nonStrikerPlayerId,
            'opening_bowler_player_id' => $bowlerPlayerId,
        ]);

        if ($this->request->isAJAX()) {
            return $this->respondWithJson([
                'success' => true,
                'message' => 'Match started. The live scoreboard is ready for the first ball.',
                'step' => 3,
                'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
            ]);
        }

        return redirect()->to('/admin/matches/' . $matchId)
            ->with('success', 'Match started. The live scoreboard is ready for the first ball.');
    }

    public function storeInnings(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $db = db_connect();
        $inningsBuilder = $db->table('match_innings');
        $existingInnings = $inningsBuilder
            ->where('match_id', $matchId)
            ->orderBy('innings_number', 'DESC')
            ->get()
            ->getResultArray();

        $inningsNumber = (int) $this->request->getPost('innings_number');
        $battingSide = (string) $this->request->getPost('batting_side');
        $bowlingSide = (string) $this->request->getPost('bowling_side');
        $completed = $this->request->getPost('completed') ? 1 : 0;
        $redirectTarget = $this->resolveReturnTarget('/admin/matches/' . $matchId);
        $errors = [];

        if ($inningsNumber < 1) {
            $errors['innings_number'] = 'Select a valid innings number.';
        }

        if (! in_array($battingSide, ['team_a', 'team_b'], true)) {
            $errors['batting_side'] = 'Select a valid batting side.';
        }

        if (! in_array($bowlingSide, ['team_a', 'team_b'], true)) {
            $errors['bowling_side'] = 'Select a valid bowling side.';
        }

        if ($battingSide === $bowlingSide && $battingSide !== '') {
            $errors['bowling_side'] = 'Batting and bowling sides must be different.';
        }

        foreach ($existingInnings as $innings) {
            if ((int) $innings['innings_number'] === $inningsNumber) {
                $errors['innings_number'] = 'That innings already exists for this match.';
                break;
            }
        }

        if ($errors !== []) {
            return redirect()->to($redirectTarget)
                ->withInput()
                ->with('innings_errors', $errors)
                ->with('error', reset($errors));
        }

        $targetRuns = null;
        if ($inningsNumber > 1) {
            $previousInnings = null;
            foreach ($existingInnings as $innings) {
                if ((int) $innings['innings_number'] === $inningsNumber - 1) {
                    $previousInnings = $innings;
                    break;
                }
            }

            if ($previousInnings !== null) {
                $targetRuns = (int) $previousInnings['runs'] + 1;
            }
        }

        $initialRequiredRunRate = $targetRuns === null
            ? null
            : round($targetRuns / max(1, (int) ($match['format_overs'] ?? 1)), 2);

        $this->matchInnings->insert([
            'match_id' => $matchId,
            'innings_number' => $inningsNumber,
            'batting_side' => $battingSide,
            'bowling_side' => $bowlingSide,
            'runs' => 0,
            'wickets' => 0,
            'overs' => 0.0,
            'balls' => 0,
            'extras' => 0,
            'byes' => 0,
            'leg_byes' => 0,
            'wides' => 0,
            'no_balls' => 0,
            'target_runs' => $targetRuns,
            'required_run_rate' => $initialRequiredRunRate,
            'completed' => $completed,
        ]);

        return redirect()->to($redirectTarget)->with('success', 'Innings created successfully.');
    }

    public function storeBall(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $participants = $this->matchFinance->getParticipants($matchId);
        $playersById = [];
        $playerSides = [];

        foreach ($participants as $participant) {
            $playerId = (int) $participant['player_id'];
            $playersById[$playerId] = $participant;
            $playerSides[$playerId] = (string) $participant['side'];
        }

        $db = db_connect();
        $inningsBuilder = $db->table('match_innings');
        $ballsBuilder = $db->table('match_balls');

        $inningsId = (int) $this->request->getPost('innings_id');
        $innings = $inningsBuilder
            ->where('id', $inningsId)
            ->where('match_id', $matchId)
            ->get()
            ->getRowArray();

        if ($innings === null) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => false,
                    'errors' => ['innings_id' => 'Select a valid innings.'],
                    'step' => 3,
                    'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
                ], 422);
            }

            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('ball_errors', ['innings_id' => 'Select a valid innings.']);
        }

        $editBallId = (int) $this->request->getPost('edit_ball_id');
        $inningsBalls = $ballsBuilder
            ->where('innings_id', $inningsId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
        $editableBall = $editBallId > 0 ? ($inningsBalls[0] ?? null) : null;

        if ($editBallId > 0 && ($editableBall === null || (int) ($editableBall['id'] ?? 0) !== $editBallId)) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => false,
                    'errors' => ['innings_id' => 'Only the latest delivery can be edited.'],
                    'step' => 3,
                    'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
                ], 422);
            }

            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('ball_errors', ['innings_id' => 'Only the latest delivery can be edited.']);
        }

        if ((int) ($innings['completed'] ?? 0) === 1 && $editBallId < 1) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => false,
                    'errors' => ['innings_id' => 'This innings is already marked as completed.'],
                    'step' => 3,
                    'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
                ], 422);
            }

            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('ball_errors', ['innings_id' => 'This innings is already marked as completed.']);
        }

        $strikerPlayerId = (int) $this->request->getPost('striker_player_id');
        $nonStrikerPlayerId = (int) $this->request->getPost('non_striker_player_id');
        $bowlerPlayerId = (int) $this->request->getPost('bowler_player_id');
        $runsBat = max(0, (int) $this->request->getPost('runs_bat'));
        $extras = max(0, (int) $this->request->getPost('extras'));
        $extraType = trim((string) $this->request->getPost('extra_type'));
        $isWicket = $this->request->getPost('is_wicket') ? 1 : 0;
        $wicketType = trim((string) $this->request->getPost('wicket_type'));
        $dismissedPlayerId = (int) $this->request->getPost('dismissed_player_id');
        $fielderPlayerId = (int) $this->request->getPost('fielder_player_id');
        $commentary = $this->emptyToNull((string) $this->request->getPost('commentary'));
        $markCompleted = $this->request->getPost('complete_innings') ? 1 : 0;
        $errors = [];

        if (! isset($playersById[$strikerPlayerId])) {
            $errors['striker_player_id'] = 'Select a valid striker.';
        }

        if (! isset($playersById[$nonStrikerPlayerId])) {
            $errors['non_striker_player_id'] = 'Select a valid non-striker.';
        }

        if (! isset($playersById[$bowlerPlayerId])) {
            $errors['bowler_player_id'] = 'Select a valid bowler.';
        }

        if ($strikerPlayerId === $nonStrikerPlayerId && $strikerPlayerId > 0) {
            $errors['non_striker_player_id'] = 'Striker and non-striker must be different players.';
        }

        $battingSide = (string) $innings['batting_side'];
        $bowlingSide = (string) $innings['bowling_side'];

        if (($playerSides[$strikerPlayerId] ?? null) !== $battingSide) {
            $errors['striker_player_id'] = 'Striker must belong to the batting side.';
        }

        if (($playerSides[$nonStrikerPlayerId] ?? null) !== $battingSide) {
            $errors['non_striker_player_id'] = 'Non-striker must belong to the batting side.';
        }

        $recentPartnershipBalls = $editBallId > 0 ? array_slice($inningsBalls, 1) : $inningsBalls;
        $dismissedBatterIds = $this->collectDismissedBatterIds($recentPartnershipBalls);

        if (in_array($strikerPlayerId, $dismissedBatterIds, true)) {
            $errors['striker_player_id'] = 'Dismissed batters cannot return as striker.';
        }

        if (in_array($nonStrikerPlayerId, $dismissedBatterIds, true)) {
            $errors['non_striker_player_id'] = 'Dismissed batters cannot return as non-striker.';
        }

        if (($playerSides[$bowlerPlayerId] ?? null) !== $bowlingSide) {
            $errors['bowler_player_id'] = 'Bowler must belong to the bowling side.';
        }

        $latestBall = $recentPartnershipBalls[0] ?? null;
        $baseRuns = max(0, (int) ($innings['runs'] ?? 0) - (int) ($editableBall['total_runs'] ?? 0));
        $baseWickets = max(0, (int) ($innings['wickets'] ?? 0) - (int) ($editableBall['is_wicket'] ?? 0));
        $baseBalls = max(0, (int) ($innings['balls'] ?? 0) - (((int) ($editableBall['is_legal_delivery'] ?? 0) === 1) ? 1 : 0));
        $baseExtras = max(0, (int) ($innings['extras'] ?? 0) - (int) ($editableBall['extras'] ?? 0));
        $baseByes = max(0, (int) ($innings['byes'] ?? 0) - (((string) ($editableBall['extra_type'] ?? '')) === 'bye' ? (int) ($editableBall['extras'] ?? 0) : 0));
        $baseLegByes = max(0, (int) ($innings['leg_byes'] ?? 0) - (((string) ($editableBall['extra_type'] ?? '')) === 'leg_bye' ? (int) ($editableBall['extras'] ?? 0) : 0));
        $baseWides = max(0, (int) ($innings['wides'] ?? 0) - (((string) ($editableBall['extra_type'] ?? '')) === 'wide' ? (int) ($editableBall['extras'] ?? 0) : 0));
        $baseNoBalls = max(0, (int) ($innings['no_balls'] ?? 0) - (((string) ($editableBall['extra_type'] ?? '')) === 'no_ball' ? (int) ($editableBall['extras'] ?? 0) : 0));
        $requiresNewBowler = $this->requiresNewOverBowler($baseBalls, $latestBall);
        $previousBowlerId = isset($latestBall['bowler_player_id']) ? (int) $latestBall['bowler_player_id'] : null;

        if ($requiresNewBowler && $previousBowlerId !== null && $bowlerPlayerId === (int) $previousBowlerId) {
            $errors['bowler_player_id'] = 'Select a new bowler at the start of each over.';
        }

        if ($extraType !== '' && ! in_array($extraType, ['wide', 'no_ball', 'bye', 'leg_bye', 'penalty'], true)) {
            $errors['extra_type'] = 'Select a valid extra type.';
        }

        $creditedBatRuns = in_array($extraType, ['wide', 'bye', 'leg_bye', 'penalty'], true)
            ? 0
            : $runsBat;

        if ($isWicket === 1) {
            if ($dismissedPlayerId < 1 || ($playerSides[$dismissedPlayerId] ?? null) !== $battingSide) {
                $errors['dismissed_player_id'] = 'Select the dismissed batter from the batting side.';
            }

            if ($wicketType === '') {
                $errors['wicket_type'] = 'Select a wicket type.';
            }

            if ($fielderPlayerId > 0 && ($playerSides[$fielderPlayerId] ?? null) !== $bowlingSide) {
                $errors['fielder_player_id'] = 'Fielder must belong to the bowling side.';
            }
        }

        $maxBalls = max(1, (int) ($match['format_overs'] ?? 0)) * 6;
        $isLegalDelivery = ! in_array($extraType, ['wide', 'no_ball'], true);

        if ((int) ($innings['balls'] ?? 0) >= $maxBalls) {
            $errors['innings_id'] = 'This innings has already reached the scheduled overs limit.';
        }

        if ($errors !== []) {
            if ($this->request->isAJAX()) {
                return $this->respondWithJson([
                    'success' => false,
                    'errors' => $errors,
                    'step' => 3,
                    'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants, $errors, (array) $this->request->getPost()),
                ], 422);
            }

            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('ball_errors', $errors);
        }

        $currentOverNumber = $editBallId > 0
            ? (int) ($editableBall['over_number'] ?? 1)
            : intdiv($baseBalls, 6) + 1;
        $nextBallInOver = $editBallId > 0
            ? (int) ($editableBall['ball_in_over'] ?? 1)
            : (int) (($ballsBuilder
                ->selectMax('ball_in_over')
                ->where('innings_id', $inningsId)
                ->where('over_number', $currentOverNumber)
                ->get()
                ->getRowArray()['ball_in_over'] ?? 0)) + 1;

        $partnershipRuns = 0;
        foreach ($recentPartnershipBalls as $ball) {
            if ((int) ($ball['is_wicket'] ?? 0) === 1) {
                break;
            }

            $partnershipRuns += (int) ($ball['total_runs'] ?? 0);
        }

        $totalRuns = $creditedBatRuns + $extras;
        $newRuns = $baseRuns + $totalRuns;
        $newWickets = $baseWickets + $isWicket;
        $newBalls = $baseBalls + ($isLegalDelivery ? 1 : 0);
        $targetRuns = $innings['target_runs'] !== null ? (int) $innings['target_runs'] : null;
        $remainingBalls = max($maxBalls - $newBalls, 0);
        $requiredRunRate = null;

        if ($targetRuns !== null && $remainingBalls > 0 && $newRuns < $targetRuns) {
            $requiredRunRate = round(($targetRuns - $newRuns) / ($remainingBalls / 6), 2);
        }

        $battingPlayerCount = count(array_filter($playerSides, static fn(string $side): bool => $side === $battingSide));
        $completed = $markCompleted === 1
            || $newBalls >= $maxBalls
            || $newWickets >= max(0, $battingPlayerCount - 1)
            || ($targetRuns !== null && $newRuns >= $targetRuns);

        $scoreAfterBall = $newRuns . '/' . $newWickets;

        $db->transStart();

        $ballPayload = [
            'match_id' => $matchId,
            'innings_id' => $inningsId,
            'striker_player_id' => $strikerPlayerId,
            'non_striker_player_id' => $nonStrikerPlayerId,
            'bowler_player_id' => $bowlerPlayerId,
            'fielder_player_id' => $fielderPlayerId > 0 ? $fielderPlayerId : null,
            'over_number' => $currentOverNumber,
            'ball_in_over' => $nextBallInOver,
            'ball_code' => $editBallId > 0
                ? $this->formatBallCode($currentOverNumber, $nextBallInOver)
                : $this->formatBallCode($currentOverNumber, $nextBallInOver),
            'runs_bat' => $creditedBatRuns,
            'extras' => $extras,
            'extra_type' => $extraType !== '' ? $extraType : null,
            'total_runs' => $totalRuns,
            'is_legal_delivery' => $isLegalDelivery ? 1 : 0,
            'is_boundary' => in_array($creditedBatRuns, [4, 6], true) ? 1 : 0,
            'wicket_type' => $isWicket === 1 ? $wicketType : null,
            'is_wicket' => $isWicket,
            'dismissed_player_id' => $isWicket === 1 && $dismissedPlayerId > 0 ? $dismissedPlayerId : null,
            'partnership_runs' => $partnershipRuns + $totalRuns,
            'score_after_ball' => $scoreAfterBall,
            'commentary' => $commentary,
        ];

        if ($editBallId > 0) {
            $this->matchBalls->update($editBallId, $ballPayload);
        } else {
            $this->matchBalls->insert($ballPayload);
        }

        $inningsUpdate = [
            'runs' => $newRuns,
            'wickets' => $newWickets,
            'overs' => $this->oversToDecimal($newBalls),
            'balls' => $newBalls,
            'extras' => $baseExtras + $extras,
            'byes' => $baseByes + ($extraType === 'bye' ? $extras : 0),
            'leg_byes' => $baseLegByes + ($extraType === 'leg_bye' ? $extras : 0),
            'wides' => $baseWides + ($extraType === 'wide' ? $extras : 0),
            'no_balls' => $baseNoBalls + ($extraType === 'no_ball' ? $extras : 0),
            'required_run_rate' => $requiredRunRate,
            'completed' => $completed ? 1 : 0,
        ];

        if ($targetRuns === null) {
            $inningsUpdate['target_runs'] = null;
        }

        $this->matchInnings->update($inningsId, $inningsUpdate);

        $db->transComplete();

        $completionPayload = $this->syncMatchCompletionState($match, $participants, $completed);

        if ($this->request->isAJAX()) {
            if ($completionPayload !== null) {
                return $this->respondWithJson([
                    'success' => true,
                    'message' => $completionPayload['message'],
                    'step' => 3,
                    'redirectUrl' => $completionPayload['redirectUrl'],
                ]);
            }

            return $this->respondWithJson([
                'success' => true,
                'message' => $editBallId > 0 ? 'Last delivery updated successfully.' : 'Ball added to the scoreboard.',
                'step' => 3,
                'scoreboardHtml' => $this->renderMatchStartScoreboard($match, $participants),
            ]);
        }

        if ($completionPayload !== null) {
            return redirect()->to($completionPayload['redirectUrl'])->with('success', $completionPayload['message']);
        }

        return redirect()->to('/admin/matches/' . $matchId)->with('success', $editBallId > 0 ? 'Last delivery updated successfully.' : 'Ball added to the scoreboard.');
    }

    public function storeExpense(int $matchId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->find($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $rules = [
            'expense_title' => 'required|min_length[2]',
            'expense_amount' => 'required|decimal|greater_than[0]',
            'expense_notes' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('expense_errors', $this->validator->getErrors());
        }

        if ($this->matchFinance->getMatchContributorIds($matchId) === []) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('match_contributor_errors', ['match_contributor_ids' => 'Set match contributors before adding expenses.']);
        }

        $expenseId = $this->matchExpenses->insert([
            'match_id' => $matchId,
            'title' => (string) $this->request->getPost('expense_title'),
            'amount' => $this->normalizeAmount((string) $this->request->getPost('expense_amount')),
            'notes' => $this->emptyToNull((string) $this->request->getPost('expense_notes')),
        ], true);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Expense added successfully.');
    }

    public function updateExpense(int $matchId, int $expenseId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $expense = $this->matchFinance->getExpenseRecord($matchId, $expenseId);

        if ($expense === null) {
            throw PageNotFoundException::forPageNotFound('Expense not found.');
        }

        $rules = [
            'expense_title' => 'required|min_length[2]',
            'expense_amount' => 'required|decimal|greater_than[0]',
            'expense_notes' => 'permit_empty|string',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('expense_update_errors', $this->validator->getErrors())
                ->with('expense_edit_id', $expenseId);
        }

        if ($this->matchFinance->getMatchContributorIds($matchId) === []) {
            return redirect()->to('/admin/matches/' . $matchId)
                ->withInput()
                ->with('match_contributor_errors', ['match_contributor_ids' => 'Set match contributors before updating expenses.'])
                ->with('expense_edit_id', $expenseId);
        }

        $this->matchExpenses->update($expenseId, [
            'title' => (string) $this->request->getPost('expense_title'),
            'amount' => $this->normalizeAmount((string) $this->request->getPost('expense_amount')),
            'notes' => $this->emptyToNull((string) $this->request->getPost('expense_notes')),
        ]);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Expense updated successfully.');
    }

    public function deleteExpense(int $matchId, int $expenseId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchFinance->getExpenseRecord($matchId, $expenseId) === null) {
            throw PageNotFoundException::forPageNotFound('Expense not found.');
        }

        $this->matchExpenses->delete($expenseId);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Expense deleted successfully.');
    }

    private function emptyToNull(string $value): ?string
    {
        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeScheduledAt(string $value): ?string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        return str_replace('T', ' ', $trimmed) . ':00';
    }

    private function normalizeAmount(string $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    private function validateAndCollectMatchPayload(?int $matchId = null)
    {
        $rules = [
            'team_name' => 'required|min_length[2]',
            'opponent_name' => 'required|min_length[2]',
            'match_type' => 'required|in_list[limited_overs,test,t10,odi,t20,friendly]',
            'format_overs' => 'required|integer|greater_than[0]',
            'venue_id' => 'permit_empty|integer|greater_than[0]',
            'scheduled_at' => 'permit_empty|valid_date[Y-m-d\TH:i]',
            'match_status' => 'required|in_list[scheduled,live,completed,abandoned,archived]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $selectedVenueId = $this->selectedVenueId();

        if ($selectedVenueId !== null && $this->venues->find($selectedVenueId) === null) {
            return redirect()->back()->withInput()->with('errors', ['venue_id' => 'Select a valid venue.']);
        }

        $teamAPlayers = $this->collectTeamPlayers('team_a_player_ids');
        $teamBPlayers = $this->collectTeamPlayers('team_b_player_ids');

        if (array_intersect($teamAPlayers, $teamBPlayers) !== []) {
            return redirect()->back()->withInput()->with('errors', ['team_b_player_ids' => 'A player cannot be selected in both teams.']);
        }

        $teamACaptainId = (int) $this->request->getPost('team_a_captain_id');
        $teamAKeeperId = (int) $this->request->getPost('team_a_keeper_id');
        $teamBCaptainId = (int) $this->request->getPost('team_b_captain_id');
        $teamBKeeperId = (int) $this->request->getPost('team_b_keeper_id');

        if ($teamACaptainId > 0 && ! in_array($teamACaptainId, $teamAPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_a_captain_id' => 'Select a Team A captain from the Team A squad.']);
        }

        if ($teamAKeeperId > 0 && ! in_array($teamAKeeperId, $teamAPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_a_keeper_id' => 'Select a Team A wicketkeeper from the Team A squad.']);
        }

        if ($teamBCaptainId > 0 && ! in_array($teamBCaptainId, $teamBPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_b_captain_id' => 'Select a Team B captain from the Team B squad.']);
        }

        if ($teamBKeeperId > 0 && ! in_array($teamBKeeperId, $teamBPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_b_keeper_id' => 'Select a Team B wicketkeeper from the Team B squad.']);
        }

        return null;
    }

    private function buildMatchDataFromRequest(?array $existingMatch = null): array
    {
        $selectedVenue = null;
        $selectedVenueId = $this->selectedVenueId();

        if ($selectedVenueId !== null) {
            $selectedVenue = $this->venues->find($selectedVenueId);
        }

        $tossWinner = $this->request->getPost('toss_winner');
        $tossDecision = $this->request->getPost('toss_decision');
        $resultType = $this->request->getPost('result_type');
        $resultSummary = $this->request->getPost('result_summary');

        return [
            'match_type' => (string) $this->request->getPost('match_type'),
            'format_overs' => (int) $this->request->getPost('format_overs'),
            'venue' => $selectedVenue['name'] ?? null,
            'venue_id' => $selectedVenue['id'] ?? null,
            'scheduled_at' => $this->normalizeScheduledAt((string) $this->request->getPost('scheduled_at')),
            'team_name' => (string) $this->request->getPost('team_name'),
            'opponent_name' => (string) $this->request->getPost('opponent_name'),
            'toss_winner' => $tossWinner === null
                ? ($existingMatch['toss_winner'] ?? null)
                : $this->emptyToNull((string) $tossWinner),
            'toss_decision' => $tossDecision === null
                ? ($existingMatch['toss_decision'] ?? null)
                : $this->emptyToNull((string) $tossDecision),
            'match_status' => (string) $this->request->getPost('match_status'),
            'result_type' => $resultType === null
                ? ($existingMatch['result_type'] ?? null)
                : $this->emptyToNull((string) $resultType),
            'result_summary' => $resultSummary === null
                ? ($existingMatch['result_summary'] ?? null)
                : $this->emptyToNull((string) $resultSummary),
            'notes' => $this->emptyToNull((string) $this->request->getPost('notes')),
        ];
    }

    private function collectTeamPlayers(string $field): array
    {
        $players = array_map('intval', (array) $this->request->getPost($field));

        return array_values(array_unique(array_filter($players)));
    }

    private function selectedVenueId(): ?int
    {
        $venueId = (int) $this->request->getPost('venue_id');

        return $venueId > 0 ? $venueId : null;
    }

    private function syncMatchPlayers(int $matchId, array $teamAPlayers, array $teamBPlayers): void
    {
        $existingPlayers = db_connect()->table('match_players')
            ->where('match_id', $matchId)
            ->get()
            ->getResultArray();

        if ($existingPlayers !== []) {
            $this->auditLogger->logBulkDelete('match_players', $existingPlayers);
        }

        $this->matchPlayers->where('match_id', $matchId)->delete();

        $teamACaptainId = (int) $this->request->getPost('team_a_captain_id');
        $teamAKeeperId = (int) $this->request->getPost('team_a_keeper_id');
        $teamBCaptainId = (int) $this->request->getPost('team_b_captain_id');
        $teamBKeeperId = (int) $this->request->getPost('team_b_keeper_id');

        foreach ($teamAPlayers as $index => $playerId) {
            $this->matchPlayers->insert([
                'match_id' => $matchId,
                'player_id' => $playerId,
                'side' => 'team_a',
                'batting_position' => $index + 1,
                'is_captain' => $playerId === $teamACaptainId ? 1 : 0,
                'is_wicketkeeper' => $playerId === $teamAKeeperId ? 1 : 0,
                'playing_xi' => 1,
            ]);
        }

        foreach ($teamBPlayers as $index => $playerId) {
            $this->matchPlayers->insert([
                'match_id' => $matchId,
                'player_id' => $playerId,
                'side' => 'team_b',
                'batting_position' => $index + 1,
                'is_captain' => $playerId === $teamBCaptainId ? 1 : 0,
                'is_wicketkeeper' => $playerId === $teamBKeeperId ? 1 : 0,
                'playing_xi' => $index < 11 ? 1 : 0,
            ]);
        }
    }

    private function getLockedParticipantIds(int $matchId): array
    {
        return $this->matchFinance->getMatchContributorIds($matchId);
    }

    private function buildScoreboardData(array $match, array $participants): array
    {
        $matchId = (int) $match['id'];
        $db = db_connect();
        $inningsRows = $db->table('match_innings')
            ->where('match_id', $matchId)
            ->orderBy('innings_number', 'ASC')
            ->get()
            ->getResultArray();

        $ballRows = $db->table('match_balls')
            ->select('match_balls.*, striker.name AS striker_name, non_striker.name AS non_striker_name, bowler.name AS bowler_name, fielder.name AS fielder_name, dismissed.name AS dismissed_name')
            ->join('players AS striker', 'striker.id = match_balls.striker_player_id', 'left')
            ->join('players AS non_striker', 'non_striker.id = match_balls.non_striker_player_id', 'left')
            ->join('players AS bowler', 'bowler.id = match_balls.bowler_player_id', 'left')
            ->join('players AS fielder', 'fielder.id = match_balls.fielder_player_id', 'left')
            ->join('players AS dismissed', 'dismissed.id = match_balls.dismissed_player_id', 'left')
            ->where('match_balls.match_id', $matchId)
            ->orderBy('match_balls.innings_id', 'ASC')
            ->orderBy('match_balls.id', 'ASC')
            ->get()
            ->getResultArray();

        $participantsBySide = [
            'team_a' => [],
            'team_b' => [],
        ];
        $playerNames = [];

        foreach ($participants as $participant) {
            $side = (string) $participant['side'];
            $participantsBySide[$side][] = $participant;
            $playerNames[(int) $participant['player_id']] = (string) $participant['name'];
        }

        $ballsByInnings = [];
        foreach ($ballRows as $ball) {
            $ballsByInnings[(int) $ball['innings_id']][] = $ball;
        }

        $inningsData = [];
        foreach ($inningsRows as $innings) {
            $inningsId = (int) $innings['id'];
            $inningBalls = $ballsByInnings[$inningsId] ?? [];
            $battingSide = (string) $innings['batting_side'];
            $bowlingSide = (string) $innings['bowling_side'];
            $batters = $participantsBySide[$battingSide] ?? [];
            $bowlers = $participantsBySide[$bowlingSide] ?? [];

            $innings['batting_label'] = $this->getSideLabel($battingSide, $match);
            $innings['bowling_label'] = $this->getSideLabel($bowlingSide, $match);
            $innings['score_text'] = $innings['runs'] . '/' . $innings['wickets'];
            $innings['overs_text'] = $this->formatOvers((int) ($innings['balls'] ?? 0));
            $innings['batting_card'] = $this->buildBattingCard($inningBalls, $batters);
            $innings['dismissed_batter_ids'] = $this->collectDismissedBatterIds($inningBalls);
            $innings['bowling_card'] = $this->buildBowlingCard($inningBalls, $bowlers);
            $innings['recent_balls'] = array_reverse(array_slice($inningBalls, -12));
            $innings['current_pair'] = $this->extractCurrentPair($inningBalls);
            $innings['requires_new_bowler'] = $this->requiresNewOverBowler(
                (int) ($innings['balls'] ?? 0),
                $inningBalls === [] ? null : $inningBalls[array_key_last($inningBalls)]
            );
            $innings['entry_defaults'] = $this->buildEntryDefaults($innings, $inningBalls, $batters, $bowlers);
            $innings['next_ball_code'] = $this->buildNextBallCode((int) ($innings['balls'] ?? 0));
            $inningsData[] = $innings;
        }

        return [
            'innings' => $inningsData,
            'playersBySide' => $participantsBySide,
            'latestInningsId' => $inningsRows === [] ? null : (int) end($inningsRows)['id'],
            'nextInningsNumber' => $inningsRows === [] ? 1 : ((int) end($inningsRows)['innings_number']) + 1,
            'wicketTypes' => ['bowled', 'caught', 'lbw', 'run_out', 'stumped', 'hit_wicket', 'retired_out'],
            'extraTypes' => ['wide', 'no_ball', 'bye', 'leg_bye', 'penalty'],
            'playerNames' => $playerNames,
            'sideLabels' => [
                'team_a' => $this->getSideLabel('team_a', $match),
                'team_b' => $this->getSideLabel('team_b', $match),
            ],
        ];
    }

    private function syncMatchCompletionState(array $match, array $participants, bool $shouldRedirectOnCompletion = false): ?array
    {
        $scoreboard = $this->buildScoreboardData($match, $participants);
        $matchId = (int) $match['id'];
        $wasCompleted = ($match['match_status'] ?? '') === 'completed';

        if (! $this->hasCompletedSecondInnings($scoreboard)) {
            if ($wasCompleted) {
                $this->matches->update($matchId, [
                    'match_status' => 'live',
                    'result_type' => null,
                    'result_summary' => null,
                ]);
            }

            return null;
        }

        $result = $this->buildCompletedMatchResult($scoreboard, $participants);

        $this->matches->update($matchId, [
            'match_status' => 'completed',
            'result_type' => $result['result_type'],
            'result_summary' => $result['result_summary'],
        ]);

        if (! $shouldRedirectOnCompletion || $wasCompleted) {
            return null;
        }

        return [
            'message' => 'Match marked as completed and full report opened.',
            'redirectUrl' => site_url('/admin/matches/' . $matchId),
        ];
    }

    private function hasCompletedSecondInnings(array $scoreboard): bool
    {
        foreach ($scoreboard['innings'] as $innings) {
            if ((int) ($innings['innings_number'] ?? 0) === 2 && (int) ($innings['completed'] ?? 0) === 1) {
                return true;
            }
        }

        return false;
    }

    private function buildCompletedMatchResult(array $scoreboard, array $participants): array
    {
        $innings = $scoreboard['innings'];

        if (count($innings) < 2) {
            return [
                'result_type' => 'completed',
                'result_summary' => 'Match completed.',
            ];
        }

        $firstInnings = $innings[0];
        $secondInnings = $innings[1];
        $firstRuns = (int) ($firstInnings['runs'] ?? 0);
        $secondRuns = (int) ($secondInnings['runs'] ?? 0);

        if ($secondRuns > $firstRuns) {
            $battingSide = (string) ($secondInnings['batting_side'] ?? '');
            $battingPlayers = array_values(array_filter(
                $participants,
                static fn(array $participant): bool => (string) ($participant['side'] ?? '') === $battingSide && (int) ($participant['playing_xi'] ?? 1) === 1
            ));

            if ($battingPlayers === []) {
                $battingPlayers = array_values(array_filter(
                    $participants,
                    static fn(array $participant): bool => (string) ($participant['side'] ?? '') === $battingSide
                ));
            }

            $wicketsRemaining = max(0, max(1, count($battingPlayers) - 1) - (int) ($secondInnings['wickets'] ?? 0));

            return [
                'result_type' => 'won',
                'result_summary' => sprintf(
                    '%s won by %d wicket%s',
                    (string) ($secondInnings['batting_label'] ?? 'Team'),
                    $wicketsRemaining,
                    $wicketsRemaining === 1 ? '' : 's'
                ),
            ];
        }

        if ($secondRuns === $firstRuns) {
            return [
                'result_type' => 'tied',
                'result_summary' => 'Match tied.',
            ];
        }

        $runMargin = $firstRuns - $secondRuns;

        return [
            'result_type' => 'won',
            'result_summary' => sprintf(
                '%s won by %d run%s',
                (string) ($firstInnings['batting_label'] ?? 'Team'),
                $runMargin,
                $runMargin === 1 ? '' : 's'
            ),
        ];
    }

    private function buildBattingCard(array $balls, array $batters): array
    {
        $card = [];

        foreach ($batters as $batter) {
            $playerId = (int) $batter['player_id'];
            $card[$playerId] = [
                'player_id' => $playerId,
                'name' => (string) $batter['name'],
                'runs' => 0,
                'balls' => 0,
                'fours' => 0,
                'sixes' => 0,
                'dismissal' => 'not out',
            ];
        }

        foreach ($balls as $ball) {
            $strikerPlayerId = (int) ($ball['striker_player_id'] ?? 0);
            if (isset($card[$strikerPlayerId])) {
                $runsBat = (int) ($ball['runs_bat'] ?? 0);
                $card[$strikerPlayerId]['runs'] += $runsBat;
                $card[$strikerPlayerId]['balls'] += (int) ($ball['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
                $card[$strikerPlayerId]['fours'] += $runsBat === 4 ? 1 : 0;
                $card[$strikerPlayerId]['sixes'] += $runsBat === 6 ? 1 : 0;
            }

            $dismissedPlayerId = (int) ($ball['dismissed_player_id'] ?? 0);
            if ((int) ($ball['is_wicket'] ?? 0) === 1 && isset($card[$dismissedPlayerId])) {
                $card[$dismissedPlayerId]['dismissal'] = $this->formatDismissal($ball);
            }
        }

        return array_values($card);
    }

    private function collectDismissedBatterIds(array $balls): array
    {
        $dismissedBatterIds = [];

        foreach ($balls as $ball) {
            if ((int) ($ball['is_wicket'] ?? 0) !== 1) {
                continue;
            }

            $dismissedPlayerId = (int) ($ball['dismissed_player_id'] ?? 0);

            if ($dismissedPlayerId > 0) {
                $dismissedBatterIds[] = $dismissedPlayerId;
            }
        }

        return array_values(array_unique($dismissedBatterIds));
    }

    private function buildBowlingCard(array $balls, array $bowlers): array
    {
        $card = [];

        foreach ($bowlers as $bowler) {
            $playerId = (int) $bowler['player_id'];
            $card[$playerId] = [
                'player_id' => $playerId,
                'name' => (string) $bowler['name'],
                'balls' => 0,
                'overs' => '0.0',
                'runs' => 0,
                'wickets' => 0,
                'economy' => null,
            ];
        }

        foreach ($balls as $ball) {
            $bowlerPlayerId = (int) ($ball['bowler_player_id'] ?? 0);

            if (! isset($card[$bowlerPlayerId])) {
                continue;
            }

            $card[$bowlerPlayerId]['balls'] += (int) ($ball['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
            $card[$bowlerPlayerId]['runs'] += (int) ($ball['total_runs'] ?? 0);

            if ((int) ($ball['is_wicket'] ?? 0) === 1 && $this->countsAsBowlerWicket((string) ($ball['wicket_type'] ?? ''))) {
                $card[$bowlerPlayerId]['wickets']++;
            }
        }

        foreach ($card as &$entry) {
            $entry['overs'] = $this->formatOvers((int) $entry['balls']);
            $entry['economy'] = (int) $entry['balls'] > 0
                ? round((float) $entry['runs'] / (((int) $entry['balls']) / 6), 2)
                : null;
        }
        unset($entry);

        return array_values(array_filter($card, static fn(array $entry): bool => $entry['balls'] > 0 || $entry['runs'] > 0 || $entry['wickets'] > 0));
    }

    private function extractCurrentPair(array $balls): array
    {
        if ($balls === []) {
            return [];
        }

        $latestBall = end($balls);

        return [
            'striker_id' => isset($latestBall['striker_player_id']) ? (int) $latestBall['striker_player_id'] : null,
            'striker' => $latestBall['striker_name'] ?? null,
            'non_striker_id' => isset($latestBall['non_striker_player_id']) ? (int) $latestBall['non_striker_player_id'] : null,
            'non_striker' => $latestBall['non_striker_name'] ?? null,
            'bowler_id' => isset($latestBall['bowler_player_id']) ? (int) $latestBall['bowler_player_id'] : null,
            'bowler' => $latestBall['bowler_name'] ?? null,
        ];
    }

    private function buildEntryDefaults(array $innings, array $balls, array $batters, array $bowlers): array
    {
        $currentPair = $this->extractCurrentPair($balls);
        $latestBall = $balls === [] ? null : $balls[array_key_last($balls)];
        $requiresNewBowler = $this->requiresNewOverBowler((int) ($innings['balls'] ?? 0), $latestBall);
        $dismissedBatterIds = $this->collectDismissedBatterIds($balls);

        if ($currentPair !== []) {
            $strikerPlayerId = $currentPair['striker_id'] ?? null;
            $nonStrikerPlayerId = $currentPair['non_striker_id'] ?? null;

            if ((int) ($latestBall['is_wicket'] ?? 0) === 1) {
                $dismissedPlayerId = isset($latestBall['dismissed_player_id']) ? (int) $latestBall['dismissed_player_id'] : null;

                if ($dismissedPlayerId !== null && $dismissedPlayerId > 0) {
                    if ($strikerPlayerId === $dismissedPlayerId) {
                        $strikerPlayerId = $this->findNextAvailableBatterId($batters, $dismissedBatterIds, [$nonStrikerPlayerId]);
                    }

                    if ($nonStrikerPlayerId === $dismissedPlayerId) {
                        $nonStrikerPlayerId = $this->findNextAvailableBatterId($batters, $dismissedBatterIds, [$strikerPlayerId]);
                    }
                }
            }

            return [
                'striker_player_id' => $strikerPlayerId,
                'non_striker_player_id' => $nonStrikerPlayerId,
                'bowler_player_id' => $requiresNewBowler ? null : ($currentPair['bowler_id'] ?? null),
                'dismissed_player_id' => $strikerPlayerId,
            ];
        }

        $openingStrikerId = isset($innings['opening_striker_player_id']) ? (int) $innings['opening_striker_player_id'] : null;
        $openingNonStrikerId = isset($innings['opening_non_striker_player_id']) ? (int) $innings['opening_non_striker_player_id'] : null;
        $openingBowlerId = isset($innings['opening_bowler_player_id']) ? (int) $innings['opening_bowler_player_id'] : null;

        if ($openingStrikerId !== null || $openingNonStrikerId !== null || $openingBowlerId !== null) {
            return [
                'striker_player_id' => $openingStrikerId,
                'non_striker_player_id' => $openingNonStrikerId,
                'bowler_player_id' => $openingBowlerId,
                'dismissed_player_id' => $openingStrikerId,
            ];
        }

        return [
            'striker_player_id' => isset($batters[0]['player_id']) ? (int) $batters[0]['player_id'] : null,
            'non_striker_player_id' => isset($batters[1]['player_id']) ? (int) $batters[1]['player_id'] : null,
            'bowler_player_id' => isset($bowlers[0]['player_id']) ? (int) $bowlers[0]['player_id'] : null,
            'dismissed_player_id' => isset($batters[0]['player_id']) ? (int) $batters[0]['player_id'] : null,
        ];
    }

    private function findNextAvailableBatterId(array $batters, array $dismissedBatterIds, array $excludedPlayerIds = []): ?int
    {
        $excludedLookup = array_fill_keys(array_filter(array_map('intval', $excludedPlayerIds)), true);
        $dismissedLookup = array_fill_keys(array_map('intval', $dismissedBatterIds), true);

        foreach ($batters as $batter) {
            $playerId = (int) ($batter['player_id'] ?? 0);

            if ($playerId < 1 || isset($dismissedLookup[$playerId]) || isset($excludedLookup[$playerId])) {
                continue;
            }

            return $playerId;
        }

        return null;
    }

    private function buildMatchStartViewData(array $match, array $participants, array $scoreboard): array
    {
        $startCompleted = $scoreboard['innings'] !== [];

        return [
            'username' => session()->get('admin_username'),
            'match' => $match,
            'participants' => $participants,
            'scoreboard' => $scoreboard,
            'sideLabels' => $scoreboard['sideLabels'],
            'formAction' => site_url('/admin/matches/' . $match['id'] . '/start'),
            'startCompleted' => $startCompleted,
            'initialWizardStep' => $startCompleted ? 3 : 1,
            'wizardScoreboardHtml' => $startCompleted ? $this->renderMatchStartScoreboard($match, $participants) : '',
        ];
    }

    private function renderMatchStartScoreboard(array $match, array $participants, array $ballErrors = [], array $ballValues = []): string
    {
        $scoreboard = $this->buildScoreboardData($match, $participants);
        $activeInnings = $this->selectWizardInnings($scoreboard);

        return view('admin/matches/_start_scoreboard', [
            'match' => $match,
            'scoreboard' => $scoreboard,
            'activeInnings' => $activeInnings,
            'ballErrors' => $ballErrors,
            'ballValues' => $ballValues,
        ]);
    }

    private function selectWizardInnings(array $scoreboard): ?array
    {
        $openInnings = array_values(array_filter(
            $scoreboard['innings'],
            static fn(array $innings): bool => (int) ($innings['completed'] ?? 0) !== 1
        ));

        if ($openInnings !== []) {
            return $openInnings[array_key_last($openInnings)];
        }

        return $scoreboard['innings'] === [] ? null : $scoreboard['innings'][array_key_last($scoreboard['innings'])];
    }

    private function resolveStartWizardStep(array $errors): int
    {
        foreach (['toss_winner_side', 'toss_decision'] as $field) {
            if (isset($errors[$field])) {
                return 1;
            }
        }

        return 2;
    }

    private function respondWithJson(array $payload, int $statusCode = 200): ResponseInterface
    {
        $payload['csrfToken'] = csrf_token();
        $payload['csrfHash'] = csrf_hash();

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($payload);
    }

    private function resolveReturnTarget(string $default): string
    {
        $returnTo = trim((string) $this->request->getPost('return_to'));

        if ($returnTo !== '' && str_starts_with($returnTo, '/admin/')) {
            return $returnTo;
        }

        return $default;
    }

    private function requiresNewOverBowler(int $completedBalls, ?array $latestBall = null): bool
    {
        if ($completedBalls <= 0 || $completedBalls % 6 !== 0) {
            return false;
        }

        $currentOverNumber = intdiv($completedBalls, 6) + 1;

        if ($latestBall !== null && (int) ($latestBall['over_number'] ?? 0) === $currentOverNumber) {
            return false;
        }

        return true;
    }

    private function buildNextBallCode(int $completedBalls): string
    {
        $overNumber = intdiv($completedBalls, 6);
        $ballInOver = ($completedBalls % 6) + 1;

        return $overNumber . '.' . $ballInOver;
    }

    private function formatBallCode(int $storedOverNumber, int $ballInOver): string
    {
        return max(0, $storedOverNumber - 1) . '.' . $ballInOver;
    }

    private function formatDismissal(array $ball): string
    {
        $wicketType = strtolower((string) ($ball['wicket_type'] ?? 'out'));
        $fielderName = trim((string) ($ball['fielder_name'] ?? ''));
        $bowlerName = trim((string) ($ball['bowler_name'] ?? ''));

        if ($wicketType === 'caught') {
            return 'c ' . ($fielderName !== '' ? $fielderName : 'fielder') . ' b ' . ($bowlerName !== '' ? $bowlerName : 'bowler');
        }

        if ($wicketType === 'stumped') {
            return 'st ' . ($fielderName !== '' ? $fielderName : 'keeper') . ' b ' . ($bowlerName !== '' ? $bowlerName : 'bowler');
        }

        if ($wicketType === 'run_out') {
            return 'run out' . ($fielderName !== '' ? ' (' . $fielderName . ')' : '');
        }

        if (in_array($wicketType, ['bowled', 'lbw', 'hit_wicket'], true)) {
            return $wicketType . ($bowlerName !== '' ? ' b ' . $bowlerName : '');
        }

        return str_replace('_', ' ', $wicketType);
    }

    private function countsAsBowlerWicket(string $wicketType): bool
    {
        return ! in_array(strtolower(trim($wicketType)), ['run_out', 'retired_hurt', 'retired_out', 'obstructing_the_field'], true);
    }

    private function formatOvers(int $balls): string
    {
        $overs = intdiv($balls, 6);
        $remainingBalls = $balls % 6;

        return $overs . '.' . $remainingBalls;
    }

    private function oversToDecimal(int $balls): float
    {
        return (float) $this->formatOvers($balls);
    }

    private function getSideLabel(string $side, array $match): string
    {
        if ($side === 'team_a') {
            return (string) $match['team_name'];
        }

        if ($side === 'team_b') {
            return (string) $match['opponent_name'];
        }

        return ucwords(str_replace('_', ' ', $side));
    }

    private function buildMatchFormViewData(?array $match = null): array
    {
        $teamAPlayerIds = [];
        $teamBPlayerIds = [];
        $teamACaptainId = null;
        $teamAKeeperId = null;
        $teamBCaptainId = null;
        $teamBKeeperId = null;

        if ($match !== null) {
            $participants = $this->matchFinance->getParticipants((int) $match['id']);

            foreach ($participants as $participant) {
                $playerId = (int) $participant['player_id'];

                if ($participant['side'] === 'team_a') {
                    $teamAPlayerIds[] = $playerId;
                    if ((int) $participant['is_captain'] === 1) {
                        $teamACaptainId = $playerId;
                    }
                    if ((int) $participant['is_wicketkeeper'] === 1) {
                        $teamAKeeperId = $playerId;
                    }
                }

                if ($participant['side'] === 'team_b') {
                    $teamBPlayerIds[] = $playerId;
                    if ((int) $participant['is_captain'] === 1) {
                        $teamBCaptainId = $playerId;
                    }
                    if ((int) $participant['is_wicketkeeper'] === 1) {
                        $teamBKeeperId = $playerId;
                    }
                }
            }
        }

        return [
            'username' => session()->get('admin_username'),
            'players' => $this->players
                ->where('status !=', 'rejected')
                ->orderBy('name', 'ASC')
                ->findAll(),
            'venues' => $this->venues->orderedList(),
            'match' => $match,
            'isMatchLocked' => $match !== null && (($match['match_status'] ?? '') === 'completed'),
            'formAction' => $match === null ? site_url('/admin/matches') : site_url('/admin/matches/' . $match['id'] . '/update'),
            'pageTitle' => $match === null ? 'Add Match' : 'Edit Match',
            'heading' => $match === null ? 'Create Match' : 'Edit Match',
            'intro' => $match === null
                ? 'Create an internal match by splitting the available players into two teams.'
                : 'Update match details, squads, and leadership assignments.',
            'submitLabel' => $match === null ? 'Create Match' : 'Update Match',
            'teamAPlayerIds' => $teamAPlayerIds,
            'teamBPlayerIds' => $teamBPlayerIds,
            'teamACaptainId' => $teamACaptainId,
            'teamAKeeperId' => $teamAKeeperId,
            'teamBCaptainId' => $teamBCaptainId,
            'teamBKeeperId' => $teamBKeeperId,
        ];
    }
}
