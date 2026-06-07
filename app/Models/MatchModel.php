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
        return $this->selectWithVenue()
            ->orderBy('scheduled_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function recentList(int $limit = 5): array
    {
        return $this->selectWithVenue()
            ->orderBy('scheduled_at', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(max(1, $limit));
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
}
