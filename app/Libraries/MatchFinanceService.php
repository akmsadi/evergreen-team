<?php

namespace App\Libraries;

use App\Models\MatchContributorModel;
use App\Models\MatchExpenseModel;
use App\Models\MatchModel;
use App\Models\MatchPlayerDepositModel;
use App\Models\MatchPlayerModel;

class MatchFinanceService
{
    private MatchExpenseModel $matchExpenses;
    private MatchModel $matches;
    private MatchContributorModel $matchContributors;
    private MatchPlayerDepositModel $matchPlayerDeposits;
    private MatchPlayerModel $matchPlayers;

    public function __construct()
    {
        $this->matchExpenses = new MatchExpenseModel();
        $this->matches = new MatchModel();
        $this->matchContributors = new MatchContributorModel();
        $this->matchPlayerDeposits = new MatchPlayerDepositModel();
        $this->matchPlayers = new MatchPlayerModel();
    }

    public function getMatchContributors(int $matchId): array
    {
        return $this->matchContributors
            ->select('match_contributors.player_id, players.name')
            ->join('players', 'players.id = match_contributors.player_id')
            ->where('match_contributors.match_id', $matchId)
            ->orderBy('players.name', 'ASC')
            ->findAll();
    }

    public function getMatchContributorIds(int $matchId): array
    {
        return array_map(static fn(array $contributor): int => (int) $contributor['player_id'], $this->getMatchContributors($matchId));
    }

    public function syncMatchContributors(int $matchId, array $contributorIds): void
    {
        $contributorIds = array_values(array_unique(array_map('intval', array_filter($contributorIds))));

        $this->matchContributors->where('match_id', $matchId)->delete();

        foreach ($contributorIds as $playerId) {
            $this->matchContributors->insert([
                'match_id' => $matchId,
                'player_id' => $playerId,
            ]);
        }
    }

    public function getParticipants(int $matchId): array
    {
        return $this->matchPlayers
            ->select('match_players.player_id, match_players.side, match_players.playing_xi, match_players.is_captain, match_players.is_wicketkeeper, players.name')
            ->join('players', 'players.id = match_players.player_id')
            ->where('match_players.match_id', $matchId)
            ->orderBy('players.name', 'ASC')
            ->findAll();
    }

    public function getParticipantIds(int $matchId): array
    {
        return array_map(static fn(array $participant): int => (int) $participant['player_id'], $this->getParticipants($matchId));
    }

    public function groupParticipantsBySide(array $participants): array
    {
        $groupedParticipants = [];

        foreach ($participants as $participant) {
            $side = $this->normalizeSideKey((string) ($participant['side'] ?? ''));

            if (! array_key_exists($side, $groupedParticipants)) {
                $groupedParticipants[$side] = [];
            }

            $groupedParticipants[$side][] = $participant;
        }

        return $groupedParticipants;
    }

    public function buildContributorGroups(array $participants, array $match): array
    {
        $participantsBySide = $this->groupParticipantsBySide($participants);
        $groups = [];

        foreach ($participantsBySide as $side => $sideParticipants) {
            $groups[] = [
                'key' => $side,
                'label' => $this->getSideLabel($side, $match),
                'participants' => $sideParticipants,
            ];
        }

        usort($groups, static function (array $left, array $right): int {
            $priority = ['team_a' => 1, 'team_b' => 2];

            return ($priority[$left['key']] ?? 99) <=> ($priority[$right['key']] ?? 99);
        });

        return $groups;
    }

    private function normalizeSideKey(string $side): string
    {
        $normalized = strtolower(trim($side));

        return match ($normalized) {
            'team_a', 'teama', 'team a', 'a', 'home', 'evergreen', 'evergreen_team', 'our_team' => 'team_a',
            'team_b', 'teamb', 'team b', 'b', 'away', 'opponent', 'opponents', 'visitor' => 'team_b',
            default => $normalized === '' ? 'unassigned' : preg_replace('/[^a-z0-9]+/', '_', $normalized),
        };
    }

    private function getSideLabel(string $side, array $match): string
    {
        return match ($side) {
            'team_a' => (string) ($match['team_name'] ?? 'Team A'),
            'team_b' => (string) ($match['opponent_name'] ?? 'Team B'),
            'unassigned' => 'Unassigned',
            default => ucwords(str_replace('_', ' ', $side)),
        };
    }

    public function getExpenses(int $matchId): array
    {
        $expenses = $this->matchExpenses
            ->where('match_id', $matchId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->attachExpenseContributors($expenses);
    }

    public function getParticipantDeposits(int $matchId): array
    {
        return $this->getDepositsByPlayerIds($this->getParticipantIds($matchId));
    }

    public function getAllDeposits(): array
    {
        return $this->matchPlayerDeposits
            ->select('match_player_deposits.*, players.name')
            ->join('players', 'players.id = match_player_deposits.player_id')
            ->orderBy('match_player_deposits.id', 'DESC')
            ->findAll();
    }

    public function getMatchExpenseSummary(): array
    {
        $matches = $this->matches->orderedList();

        if ($matches === []) {
            return [];
        }

        $expenses = $this->attachExpenseContributors($this->matchExpenses->findAll());
        $expensesByMatch = [];

        foreach ($expenses as $expense) {
            $matchId = (int) ($expense['match_id'] ?? 0);

            if ($matchId <= 0) {
                continue;
            }

            if (! isset($expensesByMatch[$matchId])) {
                $expensesByMatch[$matchId] = [
                    'expense_count' => 0,
                    'total_expense' => 0.0,
                    'contributor_ids' => [],
                ];
            }

            $expensesByMatch[$matchId]['expense_count']++;
            $expensesByMatch[$matchId]['total_expense'] += (float) ($expense['amount'] ?? 0);

            foreach (($expense['contributors'] ?? []) as $contributor) {
                $playerId = (int) ($contributor['player_id'] ?? 0);

                if ($playerId > 0) {
                    $expensesByMatch[$matchId]['contributor_ids'][$playerId] = true;
                }
            }
        }

        $summary = [];

        foreach ($matches as $match) {
            $matchId = (int) ($match['id'] ?? 0);

            if ($matchId <= 0 || ! isset($expensesByMatch[$matchId])) {
                continue;
            }

            $summary[] = [
                'match_id' => $matchId,
                'scheduled_at' => $match['scheduled_at'] ?? null,
                'team_name' => $match['team_name'] ?? '',
                'opponent_name' => $match['opponent_name'] ?? '',
                'venue' => $match['venue'] ?? '',
                'match_status' => $match['match_status'] ?? '',
                'expense_count' => $expensesByMatch[$matchId]['expense_count'],
                'total_expense' => round($expensesByMatch[$matchId]['total_expense'], 2),
                'contributor_count' => count($expensesByMatch[$matchId]['contributor_ids']),
            ];
        }

        return $summary;
    }

    public function getTopBatsmen(int $limit = 10): array
    {
        $limit = max(1, $limit);

        $rows = $this->matchPlayers->db->table('match_balls')
            ->select('match_balls.striker_player_id AS player_id, players.name, match_balls.runs_bat, match_balls.is_legal_delivery')
            ->join('players', 'players.id = match_balls.striker_player_id')
            ->join('matches', 'matches.id = match_balls.match_id')
            ->where('match_balls.striker_player_id IS NOT NULL', null, false)
            ->where('matches.match_status !=', 'archived')
            ->get()
            ->getResultArray();

        $leaderboard = [];

        foreach ($rows as $row) {
            $playerId = (int) $row['player_id'];

            if (! isset($leaderboard[$playerId])) {
                $leaderboard[$playerId] = [
                    'player_id' => $playerId,
                    'name' => (string) $row['name'],
                    'runs' => 0,
                    'balls' => 0,
                    'fours' => 0,
                    'sixes' => 0,
                    'strike_rate' => null,
                ];
            }

            $runs = (int) ($row['runs_bat'] ?? 0);
            $leaderboard[$playerId]['runs'] += $runs;
            $leaderboard[$playerId]['balls'] += (int) ($row['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
            $leaderboard[$playerId]['fours'] += $runs === 4 ? 1 : 0;
            $leaderboard[$playerId]['sixes'] += $runs === 6 ? 1 : 0;
        }

        foreach ($leaderboard as &$entry) {
            $entry['strike_rate'] = $entry['balls'] > 0
                ? round(($entry['runs'] / $entry['balls']) * 100, 2)
                : null;
        }
        unset($entry);

        $leaderboard = array_values(array_filter($leaderboard, static fn(array $entry): bool => $entry['balls'] > 0 || $entry['runs'] > 0));

        usort($leaderboard, static function (array $left, array $right): int {
            $runsComparison = $right['runs'] <=> $left['runs'];

            if ($runsComparison !== 0) {
                return $runsComparison;
            }

            $strikeRateComparison = ($right['strike_rate'] ?? -1) <=> ($left['strike_rate'] ?? -1);

            if ($strikeRateComparison !== 0) {
                return $strikeRateComparison;
            }

            $ballsComparison = $right['balls'] <=> $left['balls'];

            if ($ballsComparison !== 0) {
                return $ballsComparison;
            }

            return strcmp($left['name'], $right['name']);
        });

        return array_slice($leaderboard, 0, $limit);
    }

    public function getTopBowlers(int $limit = 10): array
    {
        $limit = max(1, $limit);

        $rows = $this->matchPlayers->db->table('match_balls')
            ->select('match_balls.bowler_player_id AS player_id, players.name, match_balls.total_runs, match_balls.is_legal_delivery, match_balls.wicket_type, match_balls.is_wicket')
            ->join('players', 'players.id = match_balls.bowler_player_id')
            ->join('matches', 'matches.id = match_balls.match_id')
            ->where('match_balls.bowler_player_id IS NOT NULL', null, false)
            ->where('matches.match_status !=', 'archived')
            ->get()
            ->getResultArray();

        $leaderboard = [];

        foreach ($rows as $row) {
            $playerId = (int) $row['player_id'];

            if (! isset($leaderboard[$playerId])) {
                $leaderboard[$playerId] = [
                    'player_id' => $playerId,
                    'name' => (string) $row['name'],
                    'balls' => 0,
                    'overs' => '0.0',
                    'runs' => 0,
                    'wickets' => 0,
                    'economy' => null,
                ];
            }

            $leaderboard[$playerId]['balls'] += (int) ($row['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
            $leaderboard[$playerId]['runs'] += (int) ($row['total_runs'] ?? 0);

            if ((int) ($row['is_wicket'] ?? 0) === 1 && $this->countsAsBowlerWicket((string) ($row['wicket_type'] ?? ''))) {
                $leaderboard[$playerId]['wickets']++;
            }
        }

        foreach ($leaderboard as &$entry) {
            $entry['overs'] = $this->formatOvers($entry['balls']);
            $entry['economy'] = $entry['balls'] > 0
                ? round($entry['runs'] / ($entry['balls'] / 6), 2)
                : null;
        }
        unset($entry);

        $leaderboard = array_values(array_filter($leaderboard, static fn(array $entry): bool => $entry['balls'] > 0));

        usort($leaderboard, static function (array $left, array $right): int {
            $wicketsComparison = $right['wickets'] <=> $left['wickets'];

            if ($wicketsComparison !== 0) {
                return $wicketsComparison;
            }

            $leftEconomy = $left['economy'] ?? PHP_FLOAT_MAX;
            $rightEconomy = $right['economy'] ?? PHP_FLOAT_MAX;
            $economyComparison = $leftEconomy <=> $rightEconomy;

            if ($economyComparison !== 0) {
                return $economyComparison;
            }

            $ballsComparison = $right['balls'] <=> $left['balls'];

            if ($ballsComparison !== 0) {
                return $ballsComparison;
            }

            return strcmp($left['name'], $right['name']);
        });

        return array_slice($leaderboard, 0, $limit);
    }

    public function getPlayerDeposits(int $playerId): array
    {
        return $this->matchPlayerDeposits
            ->select('match_player_deposits.*, players.name')
            ->join('players', 'players.id = match_player_deposits.player_id')
            ->where('match_player_deposits.player_id', $playerId)
            ->orderBy('match_player_deposits.id', 'DESC')
            ->findAll();
    }

    public function getDepositsByPlayerIds(array $playerIds): array
    {
        if ($playerIds === []) {
            return [];
        }

        return $this->matchPlayerDeposits
            ->select('match_player_deposits.*, players.name')
            ->join('players', 'players.id = match_player_deposits.player_id')
            ->whereIn('match_player_deposits.player_id', $playerIds)
            ->orderBy('match_player_deposits.id', 'DESC')
            ->findAll();
    }

    public function buildSummary(int $matchId, ?array $participants = null): array
    {
        $participants ??= $this->getParticipants($matchId);

        $summary = [];
        foreach ($participants as $participant) {
            $playerId = (int) $participant['player_id'];
            $summary[$playerId] = [
                'player_id' => $playerId,
                'name' => $participant['name'],
                'side' => $participant['side'],
                'match_owed' => 0.0,
                'owed' => 0.0,
                'deposited' => 0.0,
                'balance' => 0.0,
            ];
        }

        $expenses = $this->getExpenses($matchId);
        $playerIds = array_keys($summary);
        $matchOwedTotals = $this->calculateExpenseTotalsForPlayers($expenses, $playerIds);
        $overallOwedTotals = $this->getOverallExpenseTotalsForPlayers($playerIds);

        foreach ($matchOwedTotals as $playerId => $amount) {
            if (isset($summary[$playerId])) {
                $summary[$playerId]['match_owed'] = $amount;
            }
        }

        foreach ($overallOwedTotals as $playerId => $amount) {
            if (isset($summary[$playerId])) {
                $summary[$playerId]['owed'] = $amount;
            }
        }

        $deposits = $this->getDepositsByPlayerIds($playerIds);
        $depositTotals = $this->calculateDepositTotalsForPlayers($deposits);
        foreach ($deposits as $deposit) {
            $playerId = (int) $deposit['player_id'];

            if (isset($summary[$playerId]) && isset($depositTotals[$playerId])) {
                $summary[$playerId]['deposited'] = $depositTotals[$playerId];
            }
        }

        foreach ($summary as &$playerSummary) {
            $playerSummary['match_owed'] = round($playerSummary['match_owed'], 2);
            $playerSummary['owed'] = round($playerSummary['owed'], 2);
            $playerSummary['deposited'] = round($playerSummary['deposited'], 2);
            $playerSummary['balance'] = round($playerSummary['deposited'] - $playerSummary['owed'], 2);
            $playerSummary['status'] = $playerSummary['balance'] > 0
                ? 'overpaid'
                : ($playerSummary['balance'] < 0 ? 'underpaid' : 'settled');
        }
        unset($playerSummary);

        usort($summary, static fn(array $left, array $right): int => strcmp($left['name'], $right['name']));

        return [
            'players' => $summary,
            'totalExpense' => round(array_sum(array_map(static fn(array $expense): float => (float) $expense['amount'], $expenses)), 2),
            'totalDeposits' => round(array_sum(array_map(static fn(array $deposit): float => (float) $deposit['amount'], $deposits)), 2),
        ];
    }

    public function getExpenseRecord(int $matchId, int $expenseId): ?array
    {
        $expense = $this->matchExpenses
            ->where('match_id', $matchId)
            ->where('id', $expenseId)
            ->first();

        if ($expense === null) {
            return null;
        }

        $expense['contributor_ids'] = $this->getMatchContributorIds($matchId);

        return $expense;
    }

    public function getDepositRecord(int $matchId, int $depositId): ?array
    {
        return $this->matchPlayerDeposits->find($depositId);
    }

    public function buildPlayerOverview(array $players): array
    {
        if ($players === []) {
            return [];
        }

        $playerIds = array_map(static fn(array $player): int => (int) $player['id'], $players);
        $depositTotals = $this->calculateDepositTotalsForPlayers($this->getDepositsByPlayerIds($playerIds));
        $expenseTotals = $this->getOverallExpenseTotalsForPlayers($playerIds);
        $matchCounts = $this->getMatchCountsForPlayers($playerIds);
        $overview = [];

        foreach ($players as $player) {
            $playerId = (int) $player['id'];
            $deposited = round($depositTotals[$playerId] ?? 0.0, 2);
            $owed = round($expenseTotals[$playerId] ?? 0.0, 2);
            $balance = round($deposited - $owed, 2);

            $overview[] = [
                'player' => $player,
                'match_count' => $matchCounts[$playerId] ?? 0,
                'deposited' => $deposited,
                'owed' => $owed,
                'balance' => $balance,
                'status' => $balance > 0 ? 'overpaid' : ($balance < 0 ? 'underpaid' : 'settled'),
            ];
        }

        return $overview;
    }

    public function getPlayerMatchBreakdown(int $playerId): array
    {
        $matches = $this->matchPlayers
            ->select('match_players.match_id, match_players.side, match_players.role, match_players.batting_position, match_players.playing_xi, match_players.is_captain, match_players.is_wicketkeeper, matches.team_name, matches.opponent_name, COALESCE(venues.name, matches.venue) AS venue, matches.scheduled_at, matches.match_status')
            ->join('matches', 'matches.id = match_players.match_id')
            ->join('venues', 'venues.id = matches.venue_id', 'left')
            ->where('match_players.player_id', $playerId)
            ->orderBy('matches.scheduled_at', 'DESC')
            ->orderBy('matches.id', 'DESC')
            ->findAll();

        if ($matches === []) {
            return [];
        }

        $contributionRows = $this->matchContributors->db->query(
            'SELECT match_expenses.match_id, SUM(match_expenses.amount / contributor_counts.total_contributors) AS contribution
            FROM match_contributors
            INNER JOIN match_expenses ON match_expenses.match_id = match_contributors.match_id
            INNER JOIN (
                SELECT match_id, COUNT(*) AS total_contributors
                FROM match_contributors
                GROUP BY match_id
            ) AS contributor_counts ON contributor_counts.match_id = match_contributors.match_id
            WHERE match_contributors.player_id = ?
            GROUP BY match_expenses.match_id',
            [$playerId]
        )->getResultArray();

        $contributionsByMatch = [];
        foreach ($contributionRows as $row) {
            $contributionsByMatch[(int) $row['match_id']] = round((float) $row['contribution'], 2);
        }

        $matchIds = array_map(static fn(array $match): int => (int) $match['match_id'], $matches);
        $scoreboardStats = $this->getPlayerScoreboardStats($playerId, $matchIds);

        foreach ($matches as &$match) {
            $sideKey = $this->normalizeSideKey((string) ($match['side'] ?? ''));
            $match['side_label'] = $this->getSideLabel($sideKey, $match);
            $match['contribution'] = $contributionsByMatch[(int) $match['match_id']] ?? 0.0;
            $match['is_archived'] = ($match['match_status'] ?? '') === 'archived';
            $match['scoreboard'] = $scoreboardStats[(int) $match['match_id']] ?? $this->emptyScoreboardStats();
        }
        unset($match);

        return $matches;
    }

    private function getPlayerScoreboardStats(int $playerId, array $matchIds): array
    {
        if ($matchIds === []) {
            return [];
        }

        $rows = $this->matchPlayers->db->table('match_balls')
            ->select('match_id, striker_player_id, bowler_player_id, fielder_player_id, dismissed_player_id, runs_bat, total_runs, is_legal_delivery, wicket_type, is_wicket')
            ->whereIn('match_id', $matchIds)
            ->groupStart()
            ->where('striker_player_id', $playerId)
            ->orWhere('bowler_player_id', $playerId)
            ->orWhere('fielder_player_id', $playerId)
            ->orWhere('dismissed_player_id', $playerId)
            ->groupEnd()
            ->orderBy('match_id', 'ASC')
            ->get()
            ->getResultArray();

        $stats = [];

        foreach ($rows as $row) {
            $matchId = (int) $row['match_id'];

            if (! isset($stats[$matchId])) {
                $stats[$matchId] = $this->emptyScoreboardStats();
            }

            if ((int) ($row['striker_player_id'] ?? 0) === $playerId) {
                $stats[$matchId]['bat_runs'] += (int) ($row['runs_bat'] ?? 0);
                $stats[$matchId]['bat_balls'] += (int) ($row['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
                $stats[$matchId]['fours'] += (int) ($row['runs_bat'] ?? 0) === 4 ? 1 : 0;
                $stats[$matchId]['sixes'] += (int) ($row['runs_bat'] ?? 0) === 6 ? 1 : 0;
            }

            if ((int) ($row['dismissed_player_id'] ?? 0) === $playerId && (int) ($row['is_wicket'] ?? 0) === 1) {
                $stats[$matchId]['dismissed'] = true;
                $stats[$matchId]['dismissal_type'] = $row['wicket_type'] ?: 'out';
            }

            if ((int) ($row['bowler_player_id'] ?? 0) === $playerId) {
                $stats[$matchId]['bowl_balls'] += (int) ($row['is_legal_delivery'] ?? 0) === 1 ? 1 : 0;
                $stats[$matchId]['bowl_runs'] += (int) ($row['total_runs'] ?? 0);

                if ((int) ($row['is_wicket'] ?? 0) === 1 && $this->countsAsBowlerWicket((string) ($row['wicket_type'] ?? ''))) {
                    $stats[$matchId]['bowl_wickets']++;
                }
            }

            if ((int) ($row['fielder_player_id'] ?? 0) === $playerId && (int) ($row['is_wicket'] ?? 0) === 1) {
                $wicketType = strtolower((string) ($row['wicket_type'] ?? ''));

                if ($wicketType === 'caught') {
                    $stats[$matchId]['catches']++;
                } elseif ($wicketType === 'run_out') {
                    $stats[$matchId]['run_outs']++;
                } elseif ($wicketType === 'stumped') {
                    $stats[$matchId]['stumpings']++;
                }
            }
        }

        foreach ($stats as &$matchStats) {
            $matchStats['strike_rate'] = $matchStats['bat_balls'] > 0
                ? round(($matchStats['bat_runs'] / $matchStats['bat_balls']) * 100, 2)
                : null;
            $matchStats['bowling_overs'] = $this->formatOvers($matchStats['bowl_balls']);
            $matchStats['economy'] = $matchStats['bowl_balls'] > 0
                ? round($matchStats['bowl_runs'] / ($matchStats['bowl_balls'] / 6), 2)
                : null;
        }
        unset($matchStats);

        return $stats;
    }

    private function emptyScoreboardStats(): array
    {
        return [
            'bat_runs' => 0,
            'bat_balls' => 0,
            'fours' => 0,
            'sixes' => 0,
            'dismissed' => false,
            'dismissal_type' => null,
            'strike_rate' => null,
            'bowl_balls' => 0,
            'bowling_overs' => '0.0',
            'bowl_runs' => 0,
            'bowl_wickets' => 0,
            'economy' => null,
            'catches' => 0,
            'run_outs' => 0,
            'stumpings' => 0,
        ];
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

    private function attachExpenseContributors(array $expenses): array
    {
        if ($expenses === []) {
            return [];
        }

        $matchIds = array_values(array_unique(array_map(static fn(array $expense): int => (int) $expense['match_id'], $expenses)));
        $contributors = $this->matchContributors
            ->select('match_contributors.match_id, match_contributors.player_id, players.name')
            ->join('players', 'players.id = match_contributors.player_id')
            ->whereIn('match_contributors.match_id', $matchIds)
            ->orderBy('players.name', 'ASC')
            ->findAll();

        $contributorsByMatch = [];
        foreach ($contributors as $contributor) {
            $matchId = (int) $contributor['match_id'];
            $contributorsByMatch[$matchId][] = [
                'player_id' => (int) $contributor['player_id'],
                'name' => $contributor['name'],
            ];
        }

        foreach ($expenses as &$expense) {
            $expense['contributors'] = $contributorsByMatch[(int) $expense['match_id']] ?? [];
        }
        unset($expense);

        return $expenses;
    }

    private function calculateExpenseTotalsForPlayers(array $expenses, array $playerIds): array
    {
        $playerLookup = array_fill_keys($playerIds, true);
        $totals = [];

        foreach ($expenses as $expense) {
            $expenseContributorIds = array_map(static fn(array $contributor): int => (int) $contributor['player_id'], $expense['contributors']);
            $contributorCount = count($expenseContributorIds);

            if ($contributorCount === 0) {
                continue;
            }

            $share = round((float) $expense['amount'] / $contributorCount, 2);

            foreach ($expenseContributorIds as $playerId) {
                if (isset($playerLookup[$playerId])) {
                    $totals[$playerId] = ($totals[$playerId] ?? 0.0) + $share;
                }
            }
        }

        return $totals;
    }

    private function getOverallExpenseTotalsForPlayers(array $playerIds): array
    {
        if ($playerIds === []) {
            return [];
        }

        $linkedContributors = $this->matchContributors
            ->select('match_id')
            ->whereIn('player_id', $playerIds)
            ->findAll();

        $matchIds = array_values(array_unique(array_map(static fn(array $row): int => (int) $row['match_id'], $linkedContributors)));

        if ($matchIds === []) {
            return [];
        }

        $expenses = $this->attachExpenseContributors(
            $this->matchExpenses->whereIn('match_id', $matchIds)->findAll()
        );

        return $this->calculateExpenseTotalsForPlayers($expenses, $playerIds);
    }

    private function calculateDepositTotalsForPlayers(array $deposits): array
    {
        $totals = [];

        foreach ($deposits as $deposit) {
            $playerId = (int) $deposit['player_id'];
            $totals[$playerId] = ($totals[$playerId] ?? 0.0) + (float) $deposit['amount'];
        }

        return $totals;
    }

    private function getMatchCountsForPlayers(array $playerIds): array
    {
        if ($playerIds === []) {
            return [];
        }

        $rows = $this->matchPlayers
            ->select('player_id, COUNT(DISTINCT match_id) AS match_count')
            ->whereIn('player_id', $playerIds)
            ->groupBy('player_id')
            ->findAll();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row['player_id']] = (int) $row['match_count'];
        }

        return $counts;
    }
}
