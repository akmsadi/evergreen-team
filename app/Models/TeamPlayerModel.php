<?php

namespace App\Models;

class TeamPlayerModel extends AuditableModel
{
    protected $table      = 'team_players';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'team_id',
        'player_id',
        'name',
    ];

    public function membersByTeam(int $teamId): array
    {
        return $this->db->table('team_players')
            ->select('team_players.*, players.name AS linked_player_name, players.status AS linked_player_status')
            ->join('players', 'players.id = team_players.player_id', 'left')
            ->where('team_players.team_id', $teamId)
            ->orderBy('team_players.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
