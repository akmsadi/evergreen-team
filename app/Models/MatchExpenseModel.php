<?php

namespace App\Models;

class MatchExpenseModel extends AuditableModel
{
    protected $table = 'match_expenses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'match_id',
        'title',
        'amount',
        'notes',
    ];
}
