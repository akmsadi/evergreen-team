<?php

namespace App\Models;

class MatchInningsModel extends AuditableModel
{
    protected $table = 'match_innings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'innings_number',
        'batting_side',
        'bowling_side',
        'runs',
        'wickets',
        'overs',
        'balls',
        'extras',
        'byes',
        'leg_byes',
        'wides',
        'no_balls',
        'target_runs',
        'required_run_rate',
        'completed',
        'opening_striker_player_id',
        'opening_non_striker_player_id',
        'opening_bowler_player_id',
    ];
}
