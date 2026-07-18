<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

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

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['form', 'url', 'scoreboard']);
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

        return view('admin/matches/index-view', [
            'activeNav' => 'matches',
            'matches' => $this->matches->orderedList(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }
        return view('admin/matches/create-view', $this->buildMatchFormViewData());
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

        return view('admin/matches/create-view', $this->buildMatchFormViewData($match));
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

    public function updateStatus(int $matchId): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $status = (string) $this->request->getPost('match_status');
        $allowed = ['scheduled', 'live', 'completed', 'abandoned', 'archived'];

        if (!in_array($status, $allowed, true)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $this->matches->update($matchId, ['match_status' => $status]);

        return redirect()->to('/admin/matches/' . $matchId . '/edit')->with('success', 'Status updated to ' . ucfirst($status) . '.');
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

    public function permanentDelete(int $matchId)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->find($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        if (($match['match_status'] ?? '') !== 'archived') {
            return redirect()->to('/admin/matches')->with('error', 'Only archived matches can be permanently deleted.');
        }

        $db = db_connect();
        $expenseIds = array_column(
            $db->table('match_expenses')->select('id')->where('match_id', $matchId)->get()->getResultArray(),
            'id'
        );
        if ($expenseIds !== []) {
            $db->table('match_expense_contributors')->whereIn('expense_id', $expenseIds)->delete();
        }
        $db->table('match_expenses')->where('match_id', $matchId)->delete();
        $db->table('match_player_deposits')->where('match_id', $matchId)->delete();
        $db->table('match_contributors')->where('match_id', $matchId)->delete();
        $db->table('match_summary_batsmen')->where('match_id', $matchId)->delete();
        $db->table('match_summary_bowlers')->where('match_id', $matchId)->delete();
        $db->table('match_summary_wickets')->where('match_id', $matchId)->delete();
        $db->table('match_summary_extras')->where('match_id', $matchId)->delete();
        $db->table('match_balls')->where('match_id', $matchId)->delete();
        $db->table('match_innings')->where('match_id', $matchId)->delete();
        $db->table('match_players')->where('match_id', $matchId)->delete();
        $this->matches->delete($matchId);

        return redirect()->to('/admin/matches')->with('success', 'Match permanently deleted.');
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

        return view('admin/matches/show-view', [
            'activeNav' => 'matches',
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
            'match_type' => 'required|in_list[limited_overs,test,t10,odi,t20,friendly,football]',
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
        $teamBCaptainId = (int) $this->request->getPost('team_b_captain_id');

        if ($teamACaptainId > 0 && !in_array($teamACaptainId, $teamAPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_a_captain_id' => 'Select a Team A captain from the Team A squad.']);
        }

        if ($teamBCaptainId > 0 && !in_array($teamBCaptainId, $teamBPlayers, true)) {
            return redirect()->back()->withInput()->with('errors', ['team_b_captain_id' => 'Select a Team B captain from the Team B squad.']);
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
        $teamBCaptainId = (int) $this->request->getPost('team_b_captain_id');

        foreach ($teamAPlayers as $index => $playerId) {
            $this->matchPlayers->insert([
                'match_id' => $matchId,
                'player_id' => $playerId,
                'side' => 'team_a',
                'batting_position' => $index + 1,
                'is_captain' => $playerId === $teamACaptainId ? 1 : 0,
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
                'playing_xi' => $index < 11 ? 1 : 0,
            ]);
        }
    }

    private function getLockedParticipantIds(int $matchId): array
    {
        return $this->matchFinance->getMatchContributorIds($matchId);
    }

    private function resolveReturnTarget(string $default): string
    {
        $returnTo = trim((string) $this->request->getPost('return_to'));

        if ($returnTo !== '' && str_starts_with($returnTo, '/admin/')) {
            return $returnTo;
        }

        return $default;
    }

    private function buildMatchFormViewData(?array $match = null): array
    {
        $teamAPlayerIds = [];
        $teamBPlayerIds = [];
        $teamACaptainId = null;
        $teamBCaptainId = null;

        if ($match !== null) {
            $participants = $this->matchFinance->getParticipants((int) $match['id']);

            foreach ($participants as $participant) {
                $playerId = (int) $participant['player_id'];

                if ($participant['side'] === 'team_a') {
                    $teamAPlayerIds[] = $playerId;
                    if ((int) $participant['is_captain'] === 1) {
                        $teamACaptainId = $playerId;
                    }
                }

                if ($participant['side'] === 'team_b') {
                    $teamBPlayerIds[] = $playerId;
                    if ((int) $participant['is_captain'] === 1) {
                        $teamBCaptainId = $playerId;
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
            'heading' => $match === null ? 'Create Match' : 'Edit Match',
            'intro' => $match === null
                ? 'Create an internal match by splitting the available players into two teams.'
                : 'Update match details, squads, and leadership assignments.',
            'submitLabel' => $match === null ? 'Create Match' : 'Update Match',
            'teamAPlayerIds' => $teamAPlayerIds,
            'teamBPlayerIds' => $teamBPlayerIds,
            'teamACaptainId' => $teamACaptainId,
            'teamBCaptainId' => $teamBCaptainId,
        ];
    }

    public function editScoreboard(int $matchId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);

        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        if (($match['match_status'] ?? '') === 'completed') {
            return redirect()->to('/admin/matches/' . $matchId . '/scoreboard/preview');
        }

        $matchNumber = $this->matches->where('scheduled_at <', $match['scheduled_at'])->countAllResults() + 1;

        $db = db_connect();

        $matchPlayerRows = $db->table('match_players mp')
            ->select('mp.player_id, mp.side, mp.batting_position, p.name, p.status, guest_of.name AS guest_of_name')
            ->join('players p', 'p.id = mp.player_id')
            ->join('players guest_of', 'guest_of.id = p.guest_of_player_id', 'left')
            ->where('mp.match_id', $matchId)
            ->orderBy('mp.batting_position', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()->getResultArray();

        $teamAPlayers = array_values(array_filter($matchPlayerRows, static fn($r) => $r['side'] === 'team_a'));
        $teamBPlayers = array_values(array_filter($matchPlayerRows, static fn($r) => $r['side'] === 'team_b'));

        $tossWinner = (string) ($match['toss_winner'] ?? '');
        $tossDecision = (string) ($match['toss_decision'] ?? '');

        if ($tossWinner === 'team_a' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_a';
        } elseif ($tossWinner === 'team_a' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_a';
        } else {
            $inns1BattingSide = 'team_a';
        }

        $existingBatsmen = $db->table('match_summary_batsmen')
            ->where('match_id', $matchId)
            ->get()->getResultArray();
        $batsmenByInnings = [];
        foreach ($existingBatsmen as $row) {
            $batsmenByInnings[(int) $row['innings_number']][(int) $row['player_id']] = $row;
        }

        $existingBowlers = $db->table('match_summary_bowlers')
            ->where('match_id', $matchId)
            ->get()->getResultArray();
        $bowlersByInnings = [];
        foreach ($existingBowlers as $row) {
            $bowlersByInnings[(int) $row['innings_number']][(int) $row['player_id']] = $row;
        }

        $wickets = $db->table('match_summary_wickets msw')
            ->select('msw.*, p.name AS player_name')
            ->join('players p', 'p.id = msw.player_id', 'left')
            ->where('msw.match_id', $matchId)
            ->orderBy('msw.innings_number', 'ASC')
            ->orderBy('msw.id', 'ASC')
            ->get()->getResultArray();

        $existingExtras = $db->table('match_summary_extras')
            ->where('match_id', $matchId)
            ->get()->getResultArray();
        $extrasByInnings = [];
        foreach ($existingExtras as $row) {
            $extrasByInnings[(int) $row['innings_number']] = $row;
        }

        $allPlayers = $this->players
            ->where('status !=', 'rejected')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/matches/scoreboard-view', [
            'activeNav' => 'matches',
            'match' => $match,
            'matchNumber' => $matchNumber,
            'teamAPlayers' => $teamAPlayers,
            'teamBPlayers' => $teamBPlayers,
            'inns1BattingSide' => $inns1BattingSide,
            'batsmenByInnings' => $batsmenByInnings,
            'bowlersByInnings' => $bowlersByInnings,
            'wickets' => $wickets,
            'extrasByInnings' => $extrasByInnings,
            'allPlayers' => $allPlayers,
        ]);
    }

    public function saveToss(int $matchId): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $this->matches->update($matchId, [
            'toss_winner' => $this->emptyToNull((string) $this->request->getPost('toss_winner')),
            'toss_decision' => $this->emptyToNull((string) $this->request->getPost('toss_decision')),
        ]);

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Toss saved.');
    }

    public function saveBatting(int $matchId, int $inningsNumber): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $model = new \App\Models\MatchSummaryBatsmenModel();
        $model->where('match_id', $matchId)->where('innings_number', $inningsNumber)->delete();

        foreach ((array) $this->request->getPost('batsmen') as $playerId => $data) {
            $model->insert([
                'match_id' => $matchId,
                'innings_number' => $inningsNumber,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'player_id' => (int) $playerId,
                'runs' => ($data['runs'] ?? '') !== '' ? (int) $data['runs'] : null,
                'balls' => ($data['balls'] ?? '') !== '' ? (int) $data['balls'] : null,
                'fours' => ($data['fours'] ?? '') !== '' ? (int) $data['fours'] : null,
                'sixes' => ($data['sixes'] ?? '') !== '' ? (int) $data['sixes'] : null,
                'dismissal_mode' => $this->emptyToNull((string) ($data['dismissal_mode'] ?? '')),
                'dismissal_by' => ($data['dismissal_by'] ?? '') !== '' ? (int) $data['dismissal_by'] : null,
                'fielder_id' => ($data['fielder_id'] ?? '') !== '' ? (int) $data['fielder_id'] : null,
            ]);
        }

        $extrasData = (array) ($this->request->getPost('extras') ?? []);
        $extrasModel = new \App\Models\MatchSummaryExtrasModel();
        $extrasModel->where('match_id', $matchId)->where('innings_number', $inningsNumber)->delete();
        $extrasModel->insert([
            'match_id' => $matchId,
            'innings_number' => $inningsNumber,
            'byes' => (int) ($extrasData['byes'] ?? 0),
            'leg_byes' => (int) ($extrasData['leg_byes'] ?? 0),
            'wides' => (int) ($extrasData['wides'] ?? 0),
            'no_balls' => (int) ($extrasData['no_balls'] ?? 0),
        ]);

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Innings ' . $inningsNumber . ' batting saved.');
    }

    public function saveBowling(int $matchId, int $inningsNumber): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $model = new \App\Models\MatchSummaryBowlersModel();
        $model->where('match_id', $matchId)->where('innings_number', $inningsNumber)->delete();

        foreach ((array) $this->request->getPost('bowlers') as $playerId => $data) {
            $model->insert([
                'match_id' => $matchId,
                'innings_number' => $inningsNumber,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'player_id' => (int) $playerId,
                'overs' => ($data['overs'] ?? '') !== '' ? (float) $data['overs'] : null,
                'maidens' => ($data['maidens'] ?? '') !== '' ? (int) $data['maidens'] : null,
                'runs' => ($data['runs'] ?? '') !== '' ? (int) $data['runs'] : null,
                'wickets' => ($data['wickets'] ?? '') !== '' ? (int) $data['wickets'] : null,
            ]);
        }

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Innings ' . $inningsNumber . ' bowling saved.');
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
            'player_id' => (int) $this->request->getPost('player_id'),
            'runs' => (int) $this->request->getPost('runs') ?? 0,
            'balls' => (int) $this->request->getPost('balls') ?? 0,
            'fours' => (int) $this->request->getPost('fours') ?? 0,
            'sixes' => (int) $this->request->getPost('sixes') ?? 0,
            'dismissal_mode' => $this->emptyToNull((string) $this->request->getPost('dismissal_mode')),
            'dismissal_by' => ($this->request->getPost('dismissal_by') ?? '') !== '' ? (int) $this->request->getPost('dismissal_by') : null,
        ]);

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Batsman added.');
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
        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Batsman removed.');
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
            'player_id' => (int) $this->request->getPost('player_id'),
            'overs' => (float) $this->request->getPost('overs') ?? 0,
            'maidens' => (int) $this->request->getPost('maidens') ?? 0,
            'runs' => (int) $this->request->getPost('runs') ?? 0,
            'wickets' => (int) $this->request->getPost('wickets') ?? 0,
        ]);

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Bowler added.');
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
        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Bowler removed.');
    }

    public function completeMatch(int $matchId): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $this->matches->update($matchId, ['match_status' => 'completed']);

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard/preview')->with('success', 'Match marked as completed.');
    }

    public function scoreboardPreview(int $matchId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $match = $this->matches->findWithVenue($matchId);
        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $matchNumber = $this->matches->where('scheduled_at <', $match['scheduled_at'])->countAllResults() + 1;

        $tossWinner = (string) ($match['toss_winner'] ?? '');
        $tossDecision = (string) ($match['toss_decision'] ?? '');

        if ($tossWinner === 'team_a' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_a';
        } elseif ($tossWinner === 'team_a' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_a';
        } else {
            $inns1BattingSide = 'team_a';
        }

        $db = db_connect();

        $battingRows = $db->table('match_summary_batsmen msb')
            ->select('msb.*, p.name AS player_name, p2.name AS dismissal_by_name, p3.name AS fielder_name')
            ->join('players p', 'p.id = msb.player_id')
            ->join('players p2', 'p2.id = msb.dismissal_by', 'left')
            ->join('players p3', 'p3.id = msb.fielder_id', 'left')
            ->where('msb.match_id', $matchId)
            ->orderBy('msb.innings_number', 'ASC')
            ->orderBy('msb.sort_order', 'ASC')
            ->orderBy('msb.id', 'ASC')
            ->get()->getResultArray();

        $battingByInnings = [];
        foreach ($battingRows as $row) {
            $battingByInnings[(int) $row['innings_number']][] = $row;
        }

        $bowlingRows = $db->table('match_summary_bowlers msb')
            ->select('msb.*, p.name AS player_name')
            ->join('players p', 'p.id = msb.player_id')
            ->where('msb.match_id', $matchId)
            ->orderBy('msb.innings_number', 'ASC')
            ->orderBy('msb.sort_order', 'ASC')
            ->orderBy('msb.id', 'ASC')
            ->get()->getResultArray();

        $bowlingByInnings = [];
        foreach ($bowlingRows as $row) {
            $bowlingByInnings[(int) $row['innings_number']][] = $row;
        }

        $wicketRows = $db->table('match_summary_wickets msw')
            ->select('msw.*, p.name AS player_name')
            ->join('players p', 'p.id = msw.player_id', 'left')
            ->where('msw.match_id', $matchId)
            ->orderBy('msw.innings_number', 'ASC')
            ->orderBy('msw.id', 'ASC')
            ->get()->getResultArray();

        $wicketsByInnings = [];
        foreach ($wicketRows as $row) {
            $wicketsByInnings[(int) $row['innings_number']][] = $row;
        }

        $extrasRows = $db->table('match_summary_extras')
            ->where('match_id', $matchId)
            ->get()->getResultArray();
        $extrasByInnings = [];
        foreach ($extrasRows as $row) {
            $extrasByInnings[(int) $row['innings_number']] = $row;
        }

        $squadRows = $db->table('match_players mp')
            ->select('mp.player_id, mp.side, p.name AS player_name')
            ->join('players p', 'p.id = mp.player_id')
            ->where('mp.match_id', $matchId)
            ->orderBy('mp.batting_position', 'ASC')
            ->get()->getResultArray();

        $squadBySide = ['team_a' => [], 'team_b' => []];
        foreach ($squadRows as $row) {
            $side = $row['side'];
            if (array_key_exists($side, $squadBySide)) {
                $squadBySide[$side][] = [
                    'player_id'   => (int) $row['player_id'],
                    'player_name' => $row['player_name'],
                ];
            }
        }

        $inns1BowlingSide = $inns1BattingSide === 'team_a' ? 'team_b' : 'team_a';
        $innsSides = [
            1 => ['bowl' => $inns1BowlingSide],
            2 => ['bowl' => $inns1BattingSide],
        ];

        $didNotBowlByInnings = [];
        foreach ([1, 2] as $inns) {
            $bowlSide  = $innsSides[$inns]['bowl'];
            $bowledIds = array_map(
                static fn($r) => (int) $r['player_id'],
                array_filter(
                    $bowlingByInnings[$inns] ?? [],
                    static fn($r) => $r['overs'] !== null && (float) $r['overs'] > 0.0
                )
            );
            $didNotBowlByInnings[$inns] = array_values(array_filter(
                $squadBySide[$bowlSide],
                static fn($p) => !in_array($p['player_id'], $bowledIds, true)
            ));
        }

        return view('admin/matches/scoreboard-preview-view', [
            'activeNav' => 'matches',
            'match' => $match,
            'matchNumber' => $matchNumber,
            'inns1BattingSide' => $inns1BattingSide,
            'battingByInnings' => $battingByInnings,
            'bowlingByInnings' => $bowlingByInnings,
            'wicketsByInnings' => $wicketsByInnings,
            'extrasByInnings' => $extrasByInnings,
            'didNotBowlByInnings' => $didNotBowlByInnings,
            'publicHash' => scoreboard_encode($matchId),
        ]);
    }

    public function saveFallOfWickets(int $matchId, int $inningsNumber): ResponseInterface
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->matches->find($matchId) === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $model = new \App\Models\MatchSummaryWicketsModel();
        $model->where('match_id', $matchId)->where('innings_number', $inningsNumber)->delete();

        foreach ((array) $this->request->getPost('wickets') as $row) {
            $playerId = (int) ($row['player_id'] ?? 0);
            if ($playerId <= 0) {
                continue;
            }
            $model->insert([
                'match_id' => $matchId,
                'innings_number' => $inningsNumber,
                'player_id' => $playerId,
                'score_at_dismissal' => (int) ($row['score_at_dismissal'] ?? 0),
                'over_at_dismissal' => (float) ($row['over_at_dismissal'] ?? 0),
            ]);
        }

        return redirect()->to('/admin/matches/' . $matchId . '/scoreboard')->with('success', 'Innings ' . $inningsNumber . ' fall of wickets saved.');
    }
}
