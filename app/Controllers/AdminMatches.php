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
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/matches/index', [
            'username' => session()->get('admin_username'),
            'matches' => $this->matches->orderedList(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/matches/create', $this->buildMatchFormViewData());
    }

    public function edit(int $matchId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
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
        if (!session()->get('is_admin')) {
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
        if (!session()->get('is_admin')) {
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
        if (!session()->get('is_admin')) {
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
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $participants = $this->matchFinance->getParticipants($matchId);

        return view('admin/matches/show', [
            'username' => session()->get('admin_username'),
            'match' => $match,
            'participants' => $participants,
            'matchContributorIds' => $this->matchFinance->getMatchContributorIds($matchId),
            'matchContributors' => $this->matchFinance->getMatchContributors($matchId),
            'contributorGroups' => $this->matchFinance->buildContributorGroups($participants, $match),
            'expenses' => $this->matchFinance->getExpenses($matchId),
            'financeSummary' => $this->matchFinance->buildSummary($matchId, $participants),
        ]);
    }

    public function updateContributors(int $matchId)
    {
        if (!session()->get('is_admin')) {
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

    public function storeInnings(int $matchId)
    {
        if (!session()->get('is_admin')) {
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

        if (!in_array($battingSide, ['team_a', 'team_b'], true)) {
            $errors['batting_side'] = 'Select a valid batting side.';
        }

        if (!in_array($bowlingSide, ['team_a', 'team_b'], true)) {
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

    public function storeExpense(int $matchId)
    {
        if (!session()->get('is_admin')) {
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

        if (!$this->validate($rules)) {
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
        if (!session()->get('is_admin')) {
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

        if (!$this->validate($rules)) {
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
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matchFinance->getExpenseRecord($matchId, $expenseId) === null) {
            throw PageNotFoundException::forPageNotFound('Expense not found.');
        }

        $this->matchExpenses->delete($expenseId);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Expense deleted successfully.');
    }

    public function clearScoreboard(int $matchId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->find($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $db = db_connect();

        // Delete all balls for this match
        $db->table('match_balls')->where('match_id', $matchId)->delete();

        // Delete all innings for this match
        $db->table('match_innings')->where('match_id', $matchId)->delete();

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Match scoreboard cleared successfully. You can start a new scoreboard.');
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

        if (!$this->validate($rules)) {
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

        if ($teamACaptainId > 0 && !in_array($teamACaptainId, $teamAPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_a_captain_id' => 'Select a Team A captain from the Team A squad.']);
        }

        if ($teamAKeeperId > 0 && !in_array($teamAKeeperId, $teamAPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_a_keeper_id' => 'Select a Team A wicketkeeper from the Team A squad.']);
        }

        if ($teamBCaptainId > 0 && !in_array($teamBCaptainId, $teamBPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_b_captain_id' => 'Select a Team B captain from the Team B squad.']);
        }

        if ($teamBKeeperId > 0 && !in_array($teamBKeeperId, $teamBPlayers, true)) {
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

    public function storeSummaryBatsman(int $matchId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryBatsmen = new \App\Models\MatchSummaryBatsmenModel();
        $summaryBatsmen->insert([
            'match_id' => $matchId,
            'innings_number' => (int) $this->request->getPost('innings_number'),
            'batsman_name' => trim((string) $this->request->getPost('batsman_name')),
            'runs' => (int) $this->request->getPost('runs') ?? 0,
            'balls' => (int) $this->request->getPost('balls') ?? 0,
            'fours' => (int) $this->request->getPost('fours') ?? 0,
            'sixes' => (int) $this->request->getPost('sixes') ?? 0,
            'dismissal_mode' => $this->emptyToNull((string) $this->request->getPost('dismissal_mode')),
            'dismissal_by' => $this->emptyToNull((string) $this->request->getPost('dismissal_by')),
        ]);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Batsman added to summary.');
    }

    public function deleteSummaryBatsman(int $matchId, int $batsmanId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryBatsmen = new \App\Models\MatchSummaryBatsmenModel();
        if ($summaryBatsmen->find($batsmanId) === null) {
            throw PageNotFoundException::forPageNotFound('Batsman not found.');
        }

        $summaryBatsmen->delete($batsmanId);
        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Batsman removed from summary.');
    }

    public function storeSummaryBowler(int $matchId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryBowlers = new \App\Models\MatchSummaryBowlersModel();
        $summaryBowlers->insert([
            'match_id' => $matchId,
            'innings_number' => (int) $this->request->getPost('innings_number'),
            'bowler_name' => trim((string) $this->request->getPost('bowler_name')),
            'overs' => (float) $this->request->getPost('overs') ?? 0,
            'maidens' => (int) $this->request->getPost('maidens') ?? 0,
            'runs' => (int) $this->request->getPost('runs') ?? 0,
            'wickets' => (int) $this->request->getPost('wickets') ?? 0,
        ]);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Bowler added to summary.');
    }

    public function deleteSummaryBowler(int $matchId, int $bowlerId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryBowlers = new \App\Models\MatchSummaryBowlersModel();
        if ($summaryBowlers->find($bowlerId) === null) {
            throw PageNotFoundException::forPageNotFound('Bowler not found.');
        }

        $summaryBowlers->delete($bowlerId);
        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Bowler removed from summary.');
    }

    public function storeSummaryWicket(int $matchId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryWickets = new \App\Models\MatchSummaryWicketsModel();
        $summaryWickets->insert([
            'match_id' => $matchId,
            'innings_number' => (int) $this->request->getPost('innings_number'),
            'player_name' => trim((string) $this->request->getPost('player_name')),
            'score_at_dismissal' => (int) $this->request->getPost('score_at_dismissal'),
            'over_at_dismissal' => (float) $this->request->getPost('over_at_dismissal'),
        ]);

        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Wicket added to summary.');
    }

    public function deleteSummaryWicket(int $matchId, int $wicketId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $summaryWickets = new \App\Models\MatchSummaryWicketsModel();
        if ($summaryWickets->find($wicketId) === null) {
            throw PageNotFoundException::forPageNotFound('Wicket not found.');
        }

        $summaryWickets->delete($wicketId);
        return redirect()->to('/admin/matches/' . $matchId)->with('success', 'Wicket removed from summary.');
    }
}
