<?php

namespace App\Models;

class TeamAchievementModel extends AuditableModel
{
    protected $table = 'team_achievements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'team_id',
        'title',
        'description',
    ];

}
