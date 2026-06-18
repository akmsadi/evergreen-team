<?php

namespace App\Models;

class EventImageModel extends AuditableModel
{
    protected $table      = 'event_images';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'event_id',
        'image_path',
        'caption',
    ];

    public function forEvent(int $eventId): array
    {
        return $this->where('event_id', $eventId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
