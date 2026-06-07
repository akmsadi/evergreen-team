<?php

namespace App\Models;

class MatchExpenseContributorModel extends AuditableModel
{
    protected $table = 'match_expense_contributors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'expense_id',
        'player_id',
    ];
}
