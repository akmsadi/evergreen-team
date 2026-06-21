<?php

namespace App\Models;

class MatchSummaryBowlersModel extends AuditableModel
{
    protected $table      = 'match_summary_bowlers';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'match_id',
        'innings_number',
        'bowler_name',
        'overs',
        'maidens',
        'runs',
        'wickets',
    ];

    public function forMatchInnings(int $matchId, int $inningsNumber): array
    {
        return $this->where('match_id', $matchId)
            ->where('innings_number', $inningsNumber)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
