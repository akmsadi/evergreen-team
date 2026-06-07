<?php

namespace App\Models;

class MatchContributorModel extends AuditableModel
{
    protected $table = 'match_contributors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'player_id',
    ];
}
