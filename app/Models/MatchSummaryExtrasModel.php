<?php

namespace App\Models;

class MatchSummaryExtrasModel extends AuditableModel
{
    protected $table      = 'match_summary_extras';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'match_id',
        'innings_number',
        'byes',
        'leg_byes',
        'wides',
        'no_balls',
    ];
}
