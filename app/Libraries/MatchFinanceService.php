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
            ->select('match_players.player_id, match_players.side, match_players.playing_xi, match_players.is_captain, players.name, players.status, guest_of.name AS guest_of_name')
            ->join('players', 'players.id = match_players.player_id')
            ->join('players AS guest_of', 'guest_of.id = players.guest_of_player_id', 'left')
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

            if (!array_key_exists($side, $groupedParticipants)) {
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

    public function getGeneralExpenses(): array
    {
        $expenses = $this->matchExpenses
            ->where('match_id', null)
            ->orderBy('id', 'DESC')
            ->findAll();

        return $this->attachExpenseContributors($expenses);
    }

    public function getGeneralExpenseRecord(int $expenseId): ?array
    {
        return $this->matchExpenses
            ->where('id', $expenseId)
            ->where('match_id', null)
            ->first();
    }

    public function syncExpenseContributors(int $expenseId, array $playerIds): void
    {
        $playerIds = array_values(array_unique(array_map('intval', array_filter($playerIds))));
        $db = db_connect();
        $db->table('match_expense_contributors')->where('expense_id', $expenseId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($playerIds as $playerId) {
            $db->table('match_expense_contributors')->insert([
                'expense_id' => $expenseId,
                'player_id' => $playerId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function getExpenseContributorIds(int $expenseId): array
    {
        $rows = db_connect()
            ->table('match_expense_contributors')
            ->select('player_id')
            ->where('expense_id', $expenseId)
            ->get()
            ->getResultArray();

        return array_map(static fn(array $row): int => (int) $row['player_id'], $rows);
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

            if (!isset($expensesByMatch[$matchId])) {
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

            if ($matchId <= 0 || !isset($expensesByMatch[$matchId])) {
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

    public function getMatchExpenseChartData(int $limit = 10): array
    {
        $rows = $this->matchPlayers->db->table('matches m')
            ->select('m.id, m.team_name, m.opponent_name, m.scheduled_at, SUM(me.amount) AS total_expense')
            ->join('match_expenses me', 'me.match_id = m.id')
            ->where('m.match_status !=', 'archived')
            ->groupBy('m.id, m.team_name, m.opponent_name, m.scheduled_at')
            ->orderBy('m.scheduled_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $rows = array_reverse($rows);

        return array_map(static function (array $row): array {
            $label = $row['scheduled_at'] ? date('d M', strtotime($row['scheduled_at'])) : 'TBD';
            return [
                'label' => $label,
                'amount' => round((float) $row['total_expense'], 2),
            ];
        }, $rows);
    }

    public function getTopBatsmen(int $limit = 10): array
    {
        $limit = max(1, $limit);

        $rows = $this->matchPlayers->db->table('match_summary_batsmen msb')
            ->select('msb.player_id, players.name, msb.runs, msb.balls, msb.fours, msb.sixes, msb.dismissal_mode')
            ->join('players', 'players.id = msb.player_id')
            ->join('matches', 'matches.id = msb.match_id')
            ->where('matches.match_status !=', 'archived')
            ->get()
            ->getResultArray();

        $leaderboard = [];

        foreach ($rows as $row) {
            $playerId = (int) $row['player_id'];

            if (!isset($leaderboard[$playerId])) {
                $leaderboard[$playerId] = [
                    'player_id' => $playerId,
                    'name' => (string) $row['name'],
                    'matches_played' => 0,
                    'innings' => 0,
                    'runs' => 0,
                    'balls' => 0,
                    'fours' => 0,
                    'sixes' => 0,
                    'dismissals' => 0,
                    'best_score' => null,
                    'best_score_runs' => null,
                    'best_score_balls' => null,
                    'best_score_not_out' => null,
                ];
            }

            $hasInningsData = $row['runs'] !== null || $row['balls'] !== null || $row['dismissal_mode'] !== null;
            $inningsRuns = (int) ($row['runs'] ?? 0);
            $inningsBalls = (int) ($row['balls'] ?? 0);

            if ($hasInningsData) {
                $leaderboard[$playerId]['innings']++;
            }

            $leaderboard[$playerId]['runs'] += $inningsRuns;
            $leaderboard[$playerId]['balls'] += $inningsBalls;
            $leaderboard[$playerId]['fours'] += (int) ($row['fours'] ?? 0);
            $leaderboard[$playerId]['sixes'] += (int) ($row['sixes'] ?? 0);

            if ($row['dismissal_mode'] !== null && !in_array($row['dismissal_mode'], ['not_out', 'retired_hurt'], true)) {
                $leaderboard[$playerId]['dismissals']++;
            }

            if ($hasInningsData) {
                $bestRuns = $leaderboard[$playerId]['best_score_runs'];
                $bestBalls = $leaderboard[$playerId]['best_score_balls'];

                $isBetter = $bestRuns === null
                    || $inningsRuns > $bestRuns
                    || ($inningsRuns === $bestRuns && $inningsBalls < $bestBalls);

                if ($isBetter) {
                    $notOut = in_array($row['dismissal_mode'], ['not_out', 'retired_hurt'], true);
                    $leaderboard[$playerId]['best_score_runs'] = $inningsRuns;
                    $leaderboard[$playerId]['best_score_balls'] = $inningsBalls;
                    $leaderboard[$playerId]['best_score_not_out'] = $notOut;
                    $leaderboard[$playerId]['best_score'] = $inningsRuns . ($notOut ? '*' : '') . ' (' . $inningsBalls . ')';
                }
            }
        }

        $leaderboard = array_values(array_filter($leaderboard, static fn(array $entry): bool => $entry['balls'] > 0 || $entry['runs'] > 0));

        $matchCounts = $this->getMatchCountsForPlayers(array_column($leaderboard, 'player_id'));

        foreach ($leaderboard as &$entry) {
            $entry['matches_played'] = $matchCounts[$entry['player_id']] ?? 0;
            $entry['strike_rate'] = $entry['balls'] > 0 ? round(($entry['runs'] / $entry['balls']) * 100, 2) : null;
            $entry['average'] = $entry['dismissals'] > 0 ? round($entry['runs'] / $entry['dismissals'], 2) : null;
            unset($entry['dismissals'], $entry['best_score_runs'], $entry['best_score_balls'], $entry['best_score_not_out']);
        }
        unset($entry);

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

        $rows = $this->matchPlayers->db->table('match_summary_bowlers msbow')
            ->select('msbow.player_id, msbow.match_id, players.name, msbow.overs, msbow.runs, msbow.wickets')
            ->join('players', 'players.id = msbow.player_id')
            ->join('matches', 'matches.id = msbow.match_id')
            ->where('matches.match_status !=', 'archived')
            ->get()
            ->getResultArray();

        $leaderboard = [];

        foreach ($rows as $row) {
            $playerId = (int) $row['player_id'];

            if (!isset($leaderboard[$playerId])) {
                $leaderboard[$playerId] = [
                    'player_id' => $playerId,
                    'name' => (string) $row['name'],
                    'matches_played' => 0,
                    'innings' => 0,
                    'balls' => 0,
                    'overs' => '0.0',
                    'runs' => 0,
                    'wickets' => 0,
                    'economy' => null,
                    'best_bowling' => null,
                    'best_bowling_wickets' => null,
                    'best_bowling_runs' => null,
                ];
            }

            $overs = (float) ($row['overs'] ?? 0);
            $fullOvers = (int) $overs;
            $extraBalls = (int) round(($overs - $fullOvers) * 10);
            $ballsBowled = $fullOvers * 6 + $extraBalls;

            if ($ballsBowled > 0) {
                $leaderboard[$playerId]['innings']++;
            }

            $leaderboard[$playerId]['balls'] += $ballsBowled;
            $leaderboard[$playerId]['runs'] += (int) ($row['runs'] ?? 0);
            $leaderboard[$playerId]['wickets'] += (int) ($row['wickets'] ?? 0);

            if ($ballsBowled > 0) {
                $inningsWickets = (int) ($row['wickets'] ?? 0);
                $inningsRuns = (int) ($row['runs'] ?? 0);
                $bestWickets = $leaderboard[$playerId]['best_bowling_wickets'];
                $bestRuns = $leaderboard[$playerId]['best_bowling_runs'];

                $isBetter = $bestWickets === null
                    || $inningsWickets > $bestWickets
                    || ($inningsWickets === $bestWickets && $inningsRuns < $bestRuns);

                if ($isBetter) {
                    $leaderboard[$playerId]['best_bowling_wickets'] = $inningsWickets;
                    $leaderboard[$playerId]['best_bowling_runs'] = $inningsRuns;
                    $leaderboard[$playerId]['best_bowling'] = $inningsWickets . '/' . $inningsRuns;
                }
            }
        }

        $matchCounts = $this->getMatchCountsForPlayers(array_keys($leaderboard));

        foreach ($leaderboard as $pid => &$entry) {
            $entry['matches_played'] = $matchCounts[$pid] ?? 0;
        }
        unset($entry);

        foreach ($leaderboard as &$entry) {
            $entry['overs'] = $this->formatOvers($entry['balls']);
            $entry['economy'] = $entry['balls'] > 0
                ? round($entry['runs'] / ($entry['balls'] / 6), 2)
                : null;
        }
        unset($entry);

        $leaderboard = array_values(array_filter($leaderboard, static fn(array $entry): bool => $entry['balls'] > 0));

        foreach ($leaderboard as &$entry) {
            unset($entry['best_bowling_wickets'], $entry['best_bowling_runs']);
        }
        unset($entry);

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

    /**
     * Career and single-innings records across all non-archived matches.
     *
     * @return list<array{title: string, unit: string, note: string, rows: list<array{player_id: int|null, name: string, value: string, detail: string}>}>
     */
    public function getRecords(int $limit = 5): array
    {
        $db = $this->matchPlayers->db;
        $matchLabel = 'CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, COALESCE(v.name, m.venue, "TBD") AS venue_name, m.scheduled_at AS match_date';

        $batting = $db->table('match_summary_batsmen msb')
            ->select('msb.player_id, p.name, msb.runs, msb.balls, msb.fours, msb.sixes, msb.dismissal_mode, msb.fielder_id, ' . $matchLabel)
            ->join('players p', 'p.id = msb.player_id')
            ->join('matches m', 'm.id = msb.match_id')
            ->join('venues v', 'v.id = m.venue_id', 'left')
            ->where('m.match_status !=', 'archived')
            ->where('p.guest_of_player_id IS NULL', null, false)
            ->where('p.status', 'approved')
            ->get()->getResultArray();

        $bowling = $db->table('match_summary_bowlers msbow')
            ->select('msbow.player_id, p.name, msbow.overs, msbow.maidens, msbow.runs, msbow.wickets, ' . $matchLabel)
            ->join('players p', 'p.id = msbow.player_id')
            ->join('matches m', 'm.id = msbow.match_id')
            ->join('venues v', 'v.id = m.venue_id', 'left')
            ->where('m.match_status !=', 'archived')
            ->where('p.guest_of_player_id IS NULL', null, false)
            ->where('p.status', 'approved')
            ->get()->getResultArray();

        $catchRows = $db->table('match_summary_batsmen msb')
            ->select('msb.fielder_id AS player_id, p.name, COUNT(*) AS catches', false)
            ->join('players p', 'p.id = msb.fielder_id')
            ->join('matches m', 'm.id = msb.match_id')
            ->where('m.match_status !=', 'archived')
            ->where('p.guest_of_player_id IS NULL', null, false)
            ->where('p.status', 'approved')
            ->where('msb.dismissal_mode', 'caught')
            ->groupBy('msb.fielder_id, p.name')
            ->get()->getResultArray();

        $ballsFromOvers = static function ($overs): int {
            $overs = (float) $overs;
            $full = (int) $overs;

            return $full * 6 + (int) round(($overs - $full) * 10);
        };
        $isOut = static fn(?string $mode): bool => $mode !== null && $mode !== '' && !in_array($mode, ['not_out', 'retired_hurt'], true);
        $matchDetail = static fn(array $row): string => trim((string) $row['match_name'] . ($row['match_date'] ? ' · ' . date('d M Y', strtotime((string) $row['match_date'])) : ''));

        // Career aggregates.
        $bat = [];
        $battingInnings = [];
        foreach ($batting as $row) {
            if ($row['runs'] === null && $row['balls'] === null && $row['dismissal_mode'] === null) {
                continue;
            }

            $pid = (int) $row['player_id'];
            $bat[$pid] ??= ['name' => $row['name'], 'runs' => 0, 'balls' => 0, 'fours' => 0, 'sixes' => 0, 'outs' => 0, 'ducks' => 0, 'innings' => 0];
            $bat[$pid]['runs'] += (int) $row['runs'];
            $bat[$pid]['balls'] += (int) $row['balls'];
            $bat[$pid]['fours'] += (int) $row['fours'];
            $bat[$pid]['sixes'] += (int) $row['sixes'];
            $bat[$pid]['innings']++;

            $out = $isOut($row['dismissal_mode']);
            $bat[$pid]['outs'] += $out ? 1 : 0;
            $bat[$pid]['ducks'] += ($out && (int) $row['runs'] === 0) ? 1 : 0;
            $battingInnings[] = $row;
        }

        $bowl = [];
        $bowlingInnings = [];
        $byGround = [];
        foreach ($bowling as $row) {
            $b = $ballsFromOvers($row['overs']);
            if ($b <= 0) {
                continue;
            }

            $pid = (int) $row['player_id'];
            $bowl[$pid] ??= ['name' => $row['name'], 'balls' => 0, 'runs' => 0, 'wickets' => 0, 'maidens' => 0];
            $bowl[$pid]['balls'] += $b;
            $bowl[$pid]['runs'] += (int) $row['runs'];
            $bowl[$pid]['wickets'] += (int) $row['wickets'];
            $bowl[$pid]['maidens'] += (int) $row['maidens'];

            $ground = (string) $row['venue_name'];
            $byGround[$pid . '|' . $ground] ??= ['player_id' => $pid, 'name' => $row['name'], 'ground' => $ground, 'wickets' => 0];
            $byGround[$pid . '|' . $ground]['wickets'] += (int) $row['wickets'];

            $row['ball_count'] = $b;
            $bowlingInnings[] = $row;
        }

        $fmt = static fn(float $n): string => number_format($n, 2);
        $rank = function (array $items, callable $valueOf, bool $desc, callable $render, string $title, string $unit, string $note = '') use ($limit): array {
            usort($items, static fn(array $a, array $b): int => $desc ? $valueOf($b) <=> $valueOf($a) : $valueOf($a) <=> $valueOf($b));
            $rows = [];
            foreach (array_slice($items, 0, $limit) as $item) {
                $rows[] = $render($item);
            }

            return ['title' => $title, 'unit' => $unit, 'note' => $note, 'rows' => $rows];
        };
        $row = static fn($pid, $name, $value, string $detail = ''): array => ['player_id' => $pid !== null ? (int) $pid : null, 'name' => (string) $name, 'value' => (string) $value, 'detail' => $detail];

        $batList = array_map(static fn($pid, $b) => $b + ['player_id' => $pid], array_keys($bat), $bat);
        $bowlList = array_map(static fn($pid, $b) => $b + ['player_id' => $pid], array_keys($bowl), $bowl);
        $economy = static fn(array $b): float => $b['runs'] / ($b['balls'] / 6);
        $bowlAvg = static fn(array $b): float => $b['runs'] / $b['wickets'];

        $inningsBat = static fn(array $r): string => $r['runs'] . ($r['dismissal_mode'] === 'not_out' ? '*' : '') . ' (' . $r['balls'] . ')';

        $withAvg = array_values(array_filter($batList, static fn($b) => $b['outs'] > 0));
        $withSr = array_values(array_filter($batList, static fn($b) => $b['balls'] >= 30));
        $quickInnings = array_values(array_filter($battingInnings, static fn($r) => (int) $r['balls'] >= 10));
        $noDucks = array_values(array_filter($batList, static fn($b) => $b['ducks'] === 0));
        $hasDucks = array_values(array_filter($batList, static fn($b) => $b['ducks'] > 0));
        $withWickets = array_values(array_filter($bowlList, static fn($b) => $b['wickets'] > 0));
        $withEco = array_values(array_filter($bowlList, static fn($b) => $b['balls'] >= 12));
        $ecoInnings = array_values(array_filter($bowlingInnings, static fn($r) => $r['ball_count'] >= 6));

        $inningsEco = static fn(array $r): float => (int) $r['runs'] / ($r['ball_count'] / 6);

        return [
            $rank($battingInnings, static fn($r) => (int) $r['runs'], true, fn($r) => $row($r['player_id'], $r['name'], $inningsBat($r), $matchDetail($r)), 'Most runs in an innings', 'Runs (balls)'),
            $rank($withAvg, static fn($b) => $b['runs'] / $b['outs'], true, fn($b) => $row($b['player_id'], $b['name'], $fmt($b['runs'] / $b['outs']), $b['runs'] . ' runs, ' . $b['outs'] . ' dismissals'), 'Highest career batting average', 'Avg', 'Needs at least one dismissal'),
            $rank($withSr, static fn($b) => $b['runs'] / $b['balls'], true, fn($b) => $row($b['player_id'], $b['name'], $fmt($b['runs'] / $b['balls'] * 100), $b['runs'] . ' runs off ' . $b['balls'] . ' balls'), 'Highest career strike rate', 'SR', 'Minimum 30 balls faced'),
            $rank($quickInnings, static fn($r) => (int) $r['runs'] / (int) $r['balls'], true, fn($r) => $row($r['player_id'], $r['name'], $fmt((int) $r['runs'] / (int) $r['balls'] * 100), $inningsBat($r) . ' · ' . $matchDetail($r)), 'Highest strike rate in an innings', 'SR', 'Minimum 10 balls faced'),
            $rank($noDucks, static fn($b) => $b['innings'], true, fn($b) => $row($b['player_id'], $b['name'], $b['innings'], 'innings without a duck'), 'No ducks in career', 'Innings', 'Batters who have never been out for 0, most innings first'),
            $rank($hasDucks, static fn($b) => $b['ducks'], true, fn($b) => $row($b['player_id'], $b['name'], $b['ducks'], 'in ' . $b['innings'] . ' innings'), 'Most ducks in career', 'Ducks'),
            $rank($batList, static fn($b) => $b['sixes'], true, fn($b) => $row($b['player_id'], $b['name'], $b['sixes'], 'in ' . $b['innings'] . ' innings'), 'Most sixes in career', '6s'),
            $rank($batList, static fn($b) => $b['fours'], true, fn($b) => $row($b['player_id'], $b['name'], $b['fours'], 'in ' . $b['innings'] . ' innings'), 'Most fours in career', '4s'),
            $rank($battingInnings, static fn($r) => (int) $r['sixes'], true, fn($r) => $row($r['player_id'], $r['name'], (int) $r['sixes'], $inningsBat($r) . ' · ' . $matchDetail($r)), 'Most sixes in an innings', '6s'),
            $rank($battingInnings, static fn($r) => (int) $r['fours'], true, fn($r) => $row($r['player_id'], $r['name'], (int) $r['fours'], $inningsBat($r) . ' · ' . $matchDetail($r)), 'Most fours in an innings', '4s'),
            $rank($bowlList, static fn($b) => $b['wickets'], true, fn($b) => $row($b['player_id'], $b['name'], $b['wickets'], $this->formatOvers($b['balls']) . ' overs, ' . $b['runs'] . ' runs'), 'Most wickets in career', 'Wkts'),
            $rank($bowlingInnings, static fn($r) => (int) $r['wickets'] * 100000 - (int) $r['runs'], true, fn($r) => $row($r['player_id'], $r['name'], (int) $r['wickets'] . '/' . (int) $r['runs'], $matchDetail($r)), 'Best figures in an innings', 'W/R'),
            $rank(array_values($byGround), static fn($g) => $g['wickets'], true, fn($g) => $row($g['player_id'], $g['name'], $g['wickets'], $g['ground']), 'Most wickets on a single ground', 'Wkts'),
            $rank($withWickets, $bowlAvg, false, fn($b) => $row($b['player_id'], $b['name'], $fmt($bowlAvg($b)), $b['runs'] . ' runs, ' . $b['wickets'] . ' wickets'), 'Best career bowling average', 'Avg', 'Needs at least one wicket'),
            $rank($withEco, $economy, false, fn($b) => $row($b['player_id'], $b['name'], $fmt($economy($b)), $this->formatOvers($b['balls']) . ' overs'), 'Best career economy rate', 'Econ', 'Minimum 2 overs bowled'),
            $rank($withWickets, $bowlAvg, true, fn($b) => $row($b['player_id'], $b['name'], $fmt($bowlAvg($b)), $b['runs'] . ' runs, ' . $b['wickets'] . ' wickets'), 'Worst career bowling average', 'Avg', 'Needs at least one wicket'),
            $rank($withEco, $economy, true, fn($b) => $row($b['player_id'], $b['name'], $fmt($economy($b)), $this->formatOvers($b['balls']) . ' overs'), 'Worst career economy rate', 'Econ', 'Minimum 2 overs bowled'),
            $rank($ecoInnings, $inningsEco, true, fn($r) => $row($r['player_id'], $r['name'], $fmt($inningsEco($r)), (int) $r['runs'] . ' runs in ' . $this->formatOvers($r['ball_count']) . ' ov · ' . $matchDetail($r)), 'Worst economy rate in an innings', 'Econ', 'Minimum 1 over bowled'),
            $rank($bowlList, static fn($b) => $b['balls'], true, fn($b) => $row($b['player_id'], $b['name'], $b['balls'], $this->formatOvers($b['balls']) . ' overs'), 'Most balls bowled in career', 'Balls'),
            $rank($bowlList, static fn($b) => $b['runs'], true, fn($b) => $row($b['player_id'], $b['name'], $b['runs'], $this->formatOvers($b['balls']) . ' overs'), 'Most runs conceded in career', 'Runs'),
            $rank($bowlingInnings, static fn($r) => (int) $r['runs'], true, fn($r) => $row($r['player_id'], $r['name'], (int) $r['runs'], (int) $r['wickets'] . ' wkts · ' . $matchDetail($r)), 'Most runs conceded in an innings', 'Runs'),
            $rank($bowlList, static fn($b) => $b['maidens'], true, fn($b) => $row($b['player_id'], $b['name'], $b['maidens'], $this->formatOvers($b['balls']) . ' overs'), 'Most maidens in career', 'Maidens'),
            $rank($catchRows, static fn($c) => (int) $c['catches'], true, fn($c) => $row($c['player_id'], $c['name'], (int) $c['catches']), 'Most catches in career', 'Catches'),
        ];
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

    public function buildFinanceMatrix(array $players): array
    {
        if ($players === []) {
            return ['matches' => [], 'playerRows' => []];
        }

        $playerIds = array_map(static fn(array $p): int => (int) $p['id'], $players);

        $matches = array_reverse($this->matches->orderedList());

        $matchExpenseData = [];

        if ($matches !== []) {
            $matchIds = array_map(static fn(array $m): int => (int) $m['id'], $matches);
            $expenses = $this->attachExpenseContributors(
                $this->matchExpenses->whereIn('match_id', $matchIds)->findAll()
            );

            foreach ($expenses as $expense) {
                $mId = (int) $expense['match_id'];
                if (!isset($matchExpenseData[$mId])) {
                    $matchExpenseData[$mId] = ['total' => 0.0, 'contributors' => []];
                }
                $matchExpenseData[$mId]['total'] += (float) $expense['amount'];
                foreach ($expense['contributors'] as $contributor) {
                    $matchExpenseData[$mId]['contributors'][(int) $contributor['player_id']] = true;
                }
            }
        }

        foreach ($matches as &$match) {
            $mId = (int) $match['id'];
            $match['total_expense'] = round($matchExpenseData[$mId]['total'] ?? 0.0, 2);
            $match['contributor_count'] = count($matchExpenseData[$mId]['contributors'] ?? []);
        }
        unset($match);

        $matches = array_values(array_filter($matches, static fn(array $m): bool => $m['total_expense'] > 0));

        // General (no-match) expenses, kept separate per expense
        $generalExpensesRaw = $this->attachExpenseContributors(
            $this->matchExpenses->where('match_id', null)->findAll()
        );
        $generalExpenses = [];
        $generalShareByExpensePlayer = [];
        foreach ($generalExpensesRaw as $expense) {
            $contributorIds = array_map(static fn(array $c): int => (int) $c['player_id'], $expense['contributors']);
            $count = count($contributorIds);
            if ($count === 0) {
                continue;
            }
            $expenseId = (int) $expense['id'];
            $share = round((float) $expense['amount'] / $count, 2);
            foreach ($contributorIds as $pid) {
                $generalShareByExpensePlayer[$expenseId][$pid] = $share;
            }
            $generalExpenses[] = [
                'id' => $expenseId,
                'title' => $expense['title'],
                'amount' => round((float) $expense['amount'], 2),
                'contributor_count' => $count,
                'created_at' => $expense['created_at'] ?? null,
            ];
        }
        $generalTotal = round(array_sum(array_column($generalExpenses, 'amount')), 2);

        $financeColumns = [];

        foreach ($matches as $match) {
            $financeColumns[] = [
                'key' => 'match:' . $match['id'],
                'date' => $match['scheduled_at'] ?? null,
                'label' => getFormattedDate($match['scheduled_at'] ?? null, '-'),
                'sub_label' => $match['venue'] ?? $match['venue_name'] ?? '-',
                'header_amount_display' => $match['total_expense'] > 0 ? number_format($match['total_expense'], 0) : '-',
                'footer_amount_display' => $match['total_expense'] > 0 ? number_format($match['total_expense'], 0) : '',
            ];
        }

        foreach ($generalExpenses as $generalExpense) {
            $financeColumns[] = [
                'key' => 'general:' . $generalExpense['id'],
                'date' => $generalExpense['created_at'],
                'label' => $generalExpense['title'],
                'sub_label' => 'General',
                'header_amount_display' => number_format($generalExpense['amount'], 0),
                'footer_amount_display' => number_format($generalExpense['amount'], 0),
            ];
        }

        usort($financeColumns, static function (array $left, array $right): int {
            return ($left['date'] !== null ? strtotime($left['date']) : 0) <=> ($right['date'] !== null ? strtotime($right['date']) : 0);
        });

        $depositTotals = $this->calculateDepositTotalsForPlayers($this->getDepositsByPlayerIds($playerIds));

        $playerRows = [];
        foreach ($players as $player) {
            $playerId = (int) $player['id'];
            $matchShares = [];
            $totalOwed = 0.0;

            foreach ($matches as $match) {
                $mId = (int) $match['id'];
                $contributors = $matchExpenseData[$mId]['contributors'] ?? [];
                if (isset($contributors[$playerId]) && $match['contributor_count'] > 0) {
                    $share = round($match['total_expense'] / $match['contributor_count'], 2);
                    $matchShares[$mId] = $share;
                    $totalOwed += $share;
                }
            }

            $generalShares = [];
            $generalShareTotal = 0.0;
            foreach ($generalExpenses as $generalExpense) {
                $geId = $generalExpense['id'];
                $share = $generalShareByExpensePlayer[$geId][$playerId] ?? null;
                if ($share !== null) {
                    $generalShares[$geId] = $share;
                    $generalShareTotal += $share;
                }
            }
            $generalShareTotal = round($generalShareTotal, 2);
            $totalOwed = round($totalOwed + $generalShareTotal, 2);
            $deposited = round($depositTotals[$playerId] ?? 0.0, 2);

            $shares = [];
            foreach ($matchShares as $mId => $share) {
                $shares['match:' . $mId] = $share;
            }
            foreach ($generalShares as $geId => $share) {
                $shares['general:' . $geId] = $share;
            }

            $playerRows[] = [
                'player' => $player,
                'deposited' => $deposited,
                'owed' => $totalOwed,
                'balance' => round($deposited - $totalOwed, 2),
                'shares' => $shares,
            ];
        }

        return [
            'matches' => $matches,
            'playerRows' => $playerRows,
            'generalExpenses' => $generalExpenses,
            'generalTotal' => $generalTotal,
            'financeColumns' => $financeColumns,
        ];
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
        $scorecardMatchIds = $this->getPlayerScorecardMatchIds($playerId);

        if ($scorecardMatchIds === []) {
            return [];
        }

        $matches = $this->matchPlayers
            ->select('match_players.match_id, match_players.side, match_players.role, match_players.batting_position, match_players.playing_xi, match_players.is_captain, matches.team_name, matches.opponent_name, COALESCE(venues.name, matches.venue) AS venue, matches.scheduled_at, matches.match_status')
            ->join('matches', 'matches.id = match_players.match_id')
            ->join('venues', 'venues.id = matches.venue_id', 'left')
            ->where('match_players.player_id', $playerId)
            ->whereIn('match_players.match_id', $scorecardMatchIds)
            ->orderBy('matches.scheduled_at', 'ASC')
            ->orderBy('matches.id', 'ASC')
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

        $db = $this->matchPlayers->db;
        $stats = [];

        $batRows = $db->table('match_summary_batsmen')
            ->whereIn('match_id', $matchIds)
            ->where('player_id', $playerId)
            ->get()
            ->getResultArray();

        foreach ($batRows as $row) {
            $matchId = (int) $row['match_id'];
            if (!isset($stats[$matchId])) {
                $stats[$matchId] = $this->emptyScoreboardStats();
            }
            $stats[$matchId]['bat_runs'] += (int) ($row['runs'] ?? 0);
            $stats[$matchId]['bat_balls'] += (int) ($row['balls'] ?? 0);
            $stats[$matchId]['fours'] += (int) ($row['fours'] ?? 0);
            $stats[$matchId]['sixes'] += (int) ($row['sixes'] ?? 0);

            if ($row['runs'] !== null || $row['balls'] !== null || $row['dismissal_mode'] !== null) {
                $stats[$matchId]['batted'] = true;
            }

            $dm = $row['dismissal_mode'] ?? '';
            if ($dm !== '' && !in_array($dm, ['not_out', 'retired_hurt'], true)) {
                $stats[$matchId]['dismissed'] = true;
                $stats[$matchId]['dismissal_type'] = $dm;
            }
        }

        $bowlRows = $db->table('match_summary_bowlers')
            ->whereIn('match_id', $matchIds)
            ->where('player_id', $playerId)
            ->get()
            ->getResultArray();

        foreach ($bowlRows as $row) {
            $matchId = (int) $row['match_id'];
            if (!isset($stats[$matchId])) {
                $stats[$matchId] = $this->emptyScoreboardStats();
            }
            $overs = (float) ($row['overs'] ?? 0);
            $fullOvers = (int) $overs;
            $extraBalls = (int) round(($overs - $fullOvers) * 10);
            $ballsBowled = $fullOvers * 6 + $extraBalls;

            if ($ballsBowled > 0) {
                $stats[$matchId]['bowled'] = true;
            }

            $stats[$matchId]['bowl_balls'] += $ballsBowled;
            $stats[$matchId]['bowl_runs'] += (int) ($row['runs'] ?? 0);
            $stats[$matchId]['bowl_wickets'] += (int) ($row['wickets'] ?? 0);
        }

        $fieldRows = $db->table('match_summary_batsmen')
            ->whereIn('match_id', $matchIds)
            ->where('fielder_id', $playerId)
            ->get()
            ->getResultArray();

        foreach ($fieldRows as $row) {
            $matchId = (int) $row['match_id'];
            if (!isset($stats[$matchId])) {
                $stats[$matchId] = $this->emptyScoreboardStats();
            }
            $dm = strtolower($row['dismissal_mode'] ?? '');
            if ($dm === 'caught') {
                $stats[$matchId]['catches']++;
            } elseif ($dm === 'run_out') {
                $stats[$matchId]['run_outs']++;
            } elseif ($dm === 'stumped') {
                $stats[$matchId]['stumpings']++;
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
            'batted' => false,
            'dismissed' => false,
            'dismissal_type' => null,
            'strike_rate' => null,
            'bowl_balls' => 0,
            'bowling_overs' => '0.0',
            'bowl_runs' => 0,
            'bowl_wickets' => 0,
            'bowled' => false,
            'economy' => null,
            'catches' => 0,
            'run_outs' => 0,
            'stumpings' => 0,
        ];
    }

    private function countsAsBowlerWicket(string $wicketType): bool
    {
        return !in_array(strtolower(trim($wicketType)), ['run_out', 'retired_hurt', 'retired_out', 'obstructing_the_field'], true);
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

        $matchExpenses = [];
        $generalExpenses = [];

        foreach ($expenses as $expense) {
            if ($expense['match_id'] !== null && (int) $expense['match_id'] > 0) {
                $matchExpenses[] = $expense;
            } else {
                $generalExpenses[] = $expense;
            }
        }

        // Match-bound expenses: contributors come from match_contributors table
        if ($matchExpenses !== []) {
            $matchIds = array_values(array_unique(array_map(
                static fn(array $e): int => (int) $e['match_id'],
                $matchExpenses
            )));
            $contributors = $this->matchContributors
                ->select('match_contributors.match_id, match_contributors.player_id, players.name')
                ->join('players', 'players.id = match_contributors.player_id')
                ->whereIn('match_contributors.match_id', $matchIds)
                ->orderBy('players.name', 'ASC')
                ->findAll();

            $contributorsByMatch = [];
            foreach ($contributors as $contributor) {
                $contributorsByMatch[(int) $contributor['match_id']][] = [
                    'player_id' => (int) $contributor['player_id'],
                    'name' => $contributor['name'],
                ];
            }

            foreach ($matchExpenses as &$expense) {
                $expense['contributors'] = $contributorsByMatch[(int) $expense['match_id']] ?? [];
            }
            unset($expense);
        }

        // General (no match) expenses: contributors come from match_expense_contributors table
        if ($generalExpenses !== []) {
            $expenseIds = array_values(array_unique(array_map(
                static fn(array $e): int => (int) $e['id'],
                $generalExpenses
            )));
            $rows = db_connect()
                ->table('match_expense_contributors mec')
                ->select('mec.expense_id, mec.player_id, players.name')
                ->join('players', 'players.id = mec.player_id')
                ->whereIn('mec.expense_id', $expenseIds)
                ->orderBy('players.name', 'ASC')
                ->get()
                ->getResultArray();

            $contributorsByExpense = [];
            foreach ($rows as $row) {
                $contributorsByExpense[(int) $row['expense_id']][] = [
                    'player_id' => (int) $row['player_id'],
                    'name' => $row['name'],
                ];
            }

            foreach ($generalExpenses as &$expense) {
                $expense['contributors'] = $contributorsByExpense[(int) $expense['id']] ?? [];
            }
            unset($expense);
        }

        return array_merge($matchExpenses, $generalExpenses);
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

        // Match-bound expenses via match_contributors
        $linkedContributors = $this->matchContributors
            ->select('match_id')
            ->whereIn('player_id', $playerIds)
            ->findAll();

        $matchIds = array_values(array_unique(array_map(
            static fn(array $row): int => (int) $row['match_id'],
            $linkedContributors
        )));

        $matchExpenses = $matchIds !== []
            ? $this->attachExpenseContributors($this->matchExpenses->whereIn('match_id', $matchIds)->findAll())
            : [];

        // General expenses via match_expense_contributors
        $db = db_connect();
        $linkedRows = $db->table('match_expense_contributors')
            ->distinct()
            ->select('expense_id')
            ->whereIn('player_id', $playerIds)
            ->get()
            ->getResultArray();

        $generalExpenseIds = array_values(array_unique(array_map(
            static fn(array $row): int => (int) $row['expense_id'],
            $linkedRows
        )));

        $generalExpenses = $generalExpenseIds !== []
            ? $this->attachExpenseContributors(
                $this->matchExpenses->where('match_id', null)->whereIn('id', $generalExpenseIds)->findAll()
            )
            : [];

        $allExpenses = array_merge($matchExpenses, $generalExpenses);

        if ($allExpenses === []) {
            return [];
        }

        return $this->calculateExpenseTotalsForPlayers($allExpenses, $playerIds);
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

        $db = $this->matchPlayers->db;

        $batRows = $db->table('match_summary_batsmen')
            ->distinct()
            ->select('player_id, match_id')
            ->whereIn('player_id', $playerIds)
            ->get()
            ->getResultArray();

        $bowlRows = $db->table('match_summary_bowlers')
            ->distinct()
            ->select('player_id, match_id')
            ->whereIn('player_id', $playerIds)
            ->get()
            ->getResultArray();

        $matchIdsByPlayer = [];
        foreach (array_merge($batRows, $bowlRows) as $row) {
            $playerId = (int) $row['player_id'];
            $matchIdsByPlayer[$playerId][(int) $row['match_id']] = true;
        }

        $counts = [];
        foreach ($matchIdsByPlayer as $playerId => $matchIds) {
            $counts[$playerId] = count($matchIds);
        }

        return $counts;
    }

    private function getPlayerScorecardMatchIds(int $playerId): array
    {
        $db = $this->matchPlayers->db;

        $batMatchIds = $db->table('match_summary_batsmen')
            ->distinct()
            ->select('match_id')
            ->where('player_id', $playerId)
            ->get()
            ->getResultArray();

        $bowlMatchIds = $db->table('match_summary_bowlers')
            ->distinct()
            ->select('match_id')
            ->where('player_id', $playerId)
            ->get()
            ->getResultArray();

        $matchIds = [];
        foreach (array_merge($batMatchIds, $bowlMatchIds) as $row) {
            $matchIds[(int) $row['match_id']] = true;
        }

        return array_keys($matchIds);
    }
}
