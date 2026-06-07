<?php

namespace App\Models;

class MatchPlayerDepositModel extends AuditableModel
{
    protected $table = 'match_player_deposits';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'player_id',
        'amount',
        'notes',
    ];
}
