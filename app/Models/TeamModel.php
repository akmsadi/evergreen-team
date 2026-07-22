<?php

namespace App\Models;

class TeamModel extends AuditableModel
{
    protected $table = 'teams';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'slug',
        'slogan',
        'banner',
        'about',
        'established',
        'matches',
        'wins',
        'titles',
        'captain_id',
        'vice_captain_id',
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

    public function withCaptainAndViceCaptainBySlug($team_slug): array
    {
        return $this->db->table('teams')
            ->select('teams.*, captain.name AS captain_name, vice_captain.name AS vice_captain_name')
            ->join('players AS captain', 'captain.id = teams.captain_id', 'left')
            ->join('players AS vice_captain', 'vice_captain.id = teams.vice_captain_id', 'left')
            ->where('teams.slug', $team_slug)
            ->get()
            ->getRowArray();
    }
}