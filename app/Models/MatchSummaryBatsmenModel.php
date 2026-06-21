<?php

namespace App\Models;

class MatchSummaryBatsmenModel extends AuditableModel
{
    protected $table      = 'match_summary_batsmen';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'match_id',
        'innings_number',
        'batsman_name',
        'runs',
        'balls',
        'fours',
        'sixes',
        'dismissal_mode',
        'dismissal_by',
    ];

    public function forMatchInnings(int $matchId, int $inningsNumber): array
    {
        return $this->where('match_id', $matchId)
            ->where('innings_number', $inningsNumber)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
