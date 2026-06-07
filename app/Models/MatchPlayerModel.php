<?php

namespace App\Models;

class MatchPlayerModel extends AuditableModel
{
    protected $table = 'match_players';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'player_id',
        'side',
        'role',
        'shirt_number',
        'batting_position',
        'is_captain',
        'is_wicketkeeper',
        'playing_xi',
    ];
}
