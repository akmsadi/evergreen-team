<?php

namespace App\Models;

class VenueModel extends AuditableModel
{
    protected $table = 'venues';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
    ];

    public function orderedList(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    public function recentList(int $limit = 5): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(max(1, $limit));
    }
}
