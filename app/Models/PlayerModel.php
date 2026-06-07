<?php

namespace App\Models;

class PlayerModel extends AuditableModel
{
    protected $table = 'players';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'guest_of_player_id',
    ];

    public function recentList(int $limit = 5): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(max(1, $limit));
    }
}
