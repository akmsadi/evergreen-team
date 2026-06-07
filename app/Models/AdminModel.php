<?php

namespace App\Models;

class AdminModel extends AuditableModel
{
    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'is_active',
    ];
}
