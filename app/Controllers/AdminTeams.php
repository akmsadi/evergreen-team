<?php

namespace App\Controllers;

use App\Models\PlayerModel;
use App\Models\TeamModel;
use App\Models\TeamPlayerModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminTeams extends BaseController
{
    private PlayerModel $players;
    private TeamModel $teams;
    private TeamPlayerModel $teamPlayers;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->players = new PlayerModel();
        $this->teams = new TeamModel();
        $this->teamPlayers = new TeamPlayerModel();
    }

    public function index(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/teams/index', [
            'username' => session()->get('admin_username'),
            'teams' => $this->teams->withMemberCount(),
        ]);
    }

    public function create(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        return view('admin/teams/create', [
            'username' => session()->get('admin_username'),
        ]);
    }

    public function edit(int $teamId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $team = $this->teams->find($teamId);

        if ($team === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        return view('admin/teams/edit', [
            'username' => session()->get('admin_username'),
            'team' => $team,
            'members' => $this->teamPlayers->membersByTeam($teamId),
            'approvedPlayers' => $this->approvedPlayers(),
        ]);
    }

    public function store(): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $validationError = $this->validateTeamPayload();

        if ($validationError !== null) {
            return $validationError;
        }

        $this->teams->insert([
            'name' => $this->normalizedName(),
            'logo' => $this->uploadLogo(),
        ]);

        return redirect()->to('/admin/teams')->with('success', 'Team created successfully.');
    }

    public function update(int $teamId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->teams->find($teamId) === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        $validationError = $this->validateTeamPayload($teamId);

        if ($validationError !== null) {
            return $validationError;
        }

        $data = ['name' => $this->normalizedName()];

        $logo = $this->uploadLogo();

        if ($logo !== null) {
            $data['logo'] = $logo;
        }

        $this->teams->update($teamId, $data);

        return redirect()->to('/admin/teams/' . $teamId . '/edit')->with('success', 'Team updated successfully.');
    }

    public function delete(int $teamId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->teams->find($teamId) === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        $this->teams->delete($teamId);

        return redirect()->to('/admin/teams')->with('success', 'Team deleted successfully.');
    }

    public function storeMember(int $teamId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->teams->find($teamId) === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        $validationError = $this->validateMemberPayload($teamId);

        if ($validationError !== null) {
            return $validationError;
        }

        $this->teamPlayers->insert($this->memberPayload($teamId));

        return redirect()->to('/admin/teams/' . $teamId . '/edit')->with('success', 'Player added to team.');
    }

    public function updateMember(int $teamId, int $memberId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->teams->find($teamId) === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        if ($this->teamPlayers->find($memberId) === null) {
            throw PageNotFoundException::forPageNotFound('Team member not found.');
        }

        $validationError = $this->validateMemberPayload($teamId, $memberId);

        if ($validationError !== null) {
            return $validationError;
        }

        $this->teamPlayers->update($memberId, $this->memberPayload($teamId));

        return redirect()->to('/admin/teams/' . $teamId . '/edit')->with('success', 'Team member updated.');
    }

    public function deleteMember(int $teamId, int $memberId): ResponseInterface|string
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        if ($this->teams->find($teamId) === null) {
            throw PageNotFoundException::forPageNotFound('Team not found.');
        }

        if ($this->teamPlayers->find($memberId) === null) {
            throw PageNotFoundException::forPageNotFound('Team member not found.');
        }

        $this->teamPlayers->delete($memberId);

        return redirect()->to('/admin/teams/' . $teamId . '/edit')->with('success', 'Team member removed.');
    }

    private function validateTeamPayload(?int $teamId = null)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'logo' => 'permit_empty|uploaded[logo]|is_image[logo]|max_size[logo,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $duplicate = $this->teams->builder()
            ->where('LOWER(name)', strtolower($this->normalizedName()))
            ->get()
            ->getRowArray();

        if ($duplicate !== null && (int) $duplicate['id'] !== (int) $teamId) {
            return redirect()->back()->withInput()->with('errors', ['name' => 'A team with this name already exists.']);
        }

        return null;
    }

    private function validateMemberPayload(int $teamId, ?int $memberId = null)
    {
        $rules = [
            'member_name' => 'permit_empty|min_length[2]|max_length[255]',
            'player_id' => 'permit_empty|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $memberName = trim((string) $this->request->getPost('member_name'));
        $playerId = $this->request->getPost('player_id');

        if ($memberName === '' && ($playerId === null || $playerId === '')) {
            return redirect()->back()->withInput()->with('errors', ['member_name' => 'Provide either a player name or link to an existing player.']);
        }

        if ($playerId !== null && $playerId !== '') {
            if ($this->players->find((int) $playerId) === null) {
                return redirect()->back()->withInput()->with('errors', ['player_id' => 'Selected player not found.']);
            }
        }

        return null;
    }

    private function memberPayload(int $teamId): array
    {
        $playerId = $this->request->getPost('player_id');

        return [
            'team_id' => $teamId,
            'player_id' => ($playerId !== null && $playerId !== '') ? (int) $playerId : null,
            'name' => trim((string) $this->request->getPost('member_name')),
        ];
    }

    private function normalizedName(): string
    {
        return trim((string) $this->request->getPost('name'));
    }

    private function uploadLogo(): ?string
    {
        $file = $this->request->getFile('logo');

        if ($file === null || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $newName = $file->getRandomName();
        $file->move('assets/images/teams', $newName);

        return $newName;
    }

    private function approvedPlayers(): array
    {
        return $this->players->builder()
            ->select('id, name')
            ->where('status', 'approved')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
