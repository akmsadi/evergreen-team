<?php

namespace App\Models;

use DateTimeImmutable;

class MatchModel extends AuditableModel
{
    protected $table = 'matches';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_type',
        'format_overs',
        'venue',
        'venue_id',
        'scheduled_at',
        'team_name',
        'opponent_name',
        'toss_winner',
        'toss_decision',
        'match_status',
        'result_type',
        'result_summary',
        'notes',
    ];

    public function orderedList(): array
    {
        return $this->attachRosters(
            $this->selectWithVenue()
                ->orderBy('scheduled_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->findAll()
        );
    }

    public function scheduledList(): array
    {
        return $this->attachRosters(
            $this->selectWithVenue()
                ->where('match_status', 'scheduled')
                ->orderBy('scheduled_at', 'ASC')
                ->orderBy('id', 'ASC')
                ->findAll()
        );
    }

    public function recentList(int $limit = 5): array
    {
        return $this->selectWithVenue()
            ->orderBy('scheduled_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(max(1, $limit));
    }

    public function nextMatch(): ?array
    {
        return $this->selectWithVenue()
            ->where('scheduled_at >', date('Y-m-d H:i:s'))
            ->where('match_status !=', 'archived')
            ->orderBy('scheduled_at', 'ASC')
            ->first();
    }

    public function findWithVenue(int $matchId): ?array
    {
        return $this->selectWithVenue()
            ->where('matches.id', $matchId)
            ->first();
    }

    public function monthlyCounts(int $months = 12): array
    {
        $months = max(1, $months);
        $startMonth = new DateTimeImmutable(sprintf('first day of -%d month', $months - 1));

        $rows = $this->builder()
            ->select("DATE_FORMAT(scheduled_at, '%Y-%m') AS month_key, COUNT(*) AS total", false)
            ->where('scheduled_at >=', $startMonth->format('Y-m-d 00:00:00'))
            ->where('scheduled_at IS NOT NULL', null, false)
            ->where('match_status !=', 'archived')
            ->groupBy("DATE_FORMAT(scheduled_at, '%Y-%m')", false)
            ->orderBy('month_key', 'ASC')
            ->get()
            ->getResultArray();

        $countsByMonth = [];

        foreach ($rows as $row) {
            $countsByMonth[$row['month_key']] = (int) $row['total'];
        }

        $series = [];

        for ($offset = 0; $offset < $months; $offset++) {
            $month = $startMonth->modify(sprintf('+%d month', $offset));
            $monthKey = $month->format('Y-m');

            $series[] = [
                'label' => $month->format('M Y'),
                'count' => $countsByMonth[$monthKey] ?? 0,
            ];
        }

        return $series;
    }

    private function selectWithVenue(): self
    {
        return $this->select('matches.*, COALESCE(venues.name, matches.venue) AS venue')
            ->join('venues', 'venues.id = matches.venue_id', 'left');
    }

    private function attachRosters(array $matches): array
    {
        if ($matches === []) {
            return $matches;
        }

        $matchIds = array_column($matches, 'id');

        $rosterRows = $this->db->table('match_players')
            ->select('match_players.match_id, match_players.side, match_players.is_captain, players.app_id AS player_id')
            ->join('players', 'players.id = match_players.player_id')
            ->whereIn('match_players.match_id', $matchIds)
            ->get()
            ->getResultArray();

        $rosterByMatch = [];

        foreach ($rosterRows as $row) {
            if (!in_array($row['side'], ['team_a', 'team_b'], true)) {
                continue;
            }

            $rosterByMatch[$row['match_id']][$row['side']][] = [
                'player_id' => $row['player_id'],
                'is_captain' => (bool) $row['is_captain'],
            ];
        }

        foreach ($matches as &$match) {
            $match['team_a_players'] = $rosterByMatch[$match['id']]['team_a'] ?? [];
            $match['team_b_players'] = $rosterByMatch[$match['id']]['team_b'] ?? [];
        }
        unset($match);

        return $matches;
    }
}
