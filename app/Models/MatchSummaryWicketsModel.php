<?php

namespace App\Models;

class MatchSummaryWicketsModel extends AuditableModel
{
    protected $table      = 'match_summary_wickets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'match_id',
        'innings_number',
        'player_id',
        'score_at_dismissal',
        'over_at_dismissal',
    ];

    public function forMatchInnings(int $matchId, int $inningsNumber): array
    {
        return $this->where('match_id', $matchId)
            ->where('innings_number', $inningsNumber)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
