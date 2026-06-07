<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminHistoryModel extends Model
{
    protected $table = 'admin_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'admin_id',
        'admin_username',
        'action',
        'table_name',
        'row_id',
        'old_values',
        'new_values',
        'request_method',
        'request_path',
        'created_at',
    ];
}
