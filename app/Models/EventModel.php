<?php

namespace App\Models;

class EventModel extends AuditableModel
{
    protected $table      = 'events';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'title',
        'description',
    ];

    public function orderedList(): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
