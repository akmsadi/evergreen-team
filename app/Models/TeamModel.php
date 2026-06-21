<?php

namespace App\Models;

class TeamModel extends AuditableModel
{
    protected $table      = 'teams';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'name',
        'logo',
    ];

    public function withMemberCount(): array
    {
        return $this->db->table('teams')
            ->select('teams.*, COUNT(team_players.id) AS member_count')
            ->join('team_players', 'team_players.team_id = teams.id', 'left')
            ->groupBy('teams.id')
            ->orderBy('teams.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
