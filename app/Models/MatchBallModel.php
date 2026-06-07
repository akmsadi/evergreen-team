<?php

namespace App\Models;

class MatchBallModel extends AuditableModel
{
    protected $table = 'match_balls';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'innings_id',
        'striker_player_id',
        'non_striker_player_id',
        'bowler_player_id',
        'fielder_player_id',
        'over_number',
        'ball_in_over',
        'ball_code',
        'runs_bat',
        'extras',
        'extra_type',
        'total_runs',
        'is_legal_delivery',
        'is_boundary',
        'wicket_type',
        'is_wicket',
        'dismissed_player_id',
        'partnership_runs',
        'score_after_ball',
        'commentary',
    ];
}
