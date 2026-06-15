<?php

namespace App\Controllers;

use App\Models\PlayerModel;
use App\Models\MatchModel;

class Home extends BaseController
{
    private PlayerModel $players;
    private MatchModel $matches;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->players = new PlayerModel();
        $this->matches = new MatchModel();
    }

    public function index(): string
    {
        $lastMatch = null;
        $recentMatches = $this->matches->recentList(1);
        if (!empty($recentMatches)) {
            $lastMatch = $recentMatches[0];
        }

        $nextMatch = $this->matches->nextMatch();

        return view('index-view', [
            'playerCount'  => $this->players->where('status', 'approved')->countAllResults(),
            'pendingCount' => $this->players->where('status', 'pending')->countAllResults(),
            'lastMatch'    => $lastMatch,
            'nextMatch'    => $nextMatch,
        ]);
    }

    public function joinUs()
    {
        $rules = [
            'name'    => 'required|min_length[2]',
            'email'   => 'permit_empty|valid_email',
            'phone'   => 'required|min_length[7]',
            'address' => 'required|min_length[5]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('/') . '#join')
                ->withInput()
                ->with('join_errors', $this->validator->getErrors());
        }

        $playerData = [
            'name'    => (string) $this->request->getPost('name'),
            'email'   => (string) $this->request->getPost('email'),
            'phone'   => (string) $this->request->getPost('phone'),
            'address' => (string) $this->request->getPost('address'),
            'status'  => 'pending',
        ];

        $this->players->insert($playerData);

        return redirect()->to(site_url('/') . '#join')
            ->with('join_success', 'Thanks. Your player details were saved with pending status.');
    }
}
