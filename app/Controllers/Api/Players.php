<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PlayerModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Players extends BaseController
{
    private PlayerModel $players;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->players = new PlayerModel();
    }

    public function index(): ResponseInterface
    {
        $players = $this->players->orderBy('name', 'ASC')->findAll();
        $players = array_map(function ($player) {
            $player['server_id'] = $player['id'];
            $player['id'] = $player['app_id'];
            unset($player['app_id']);
            return $player;
        }, $players);

        return $this->response->setJSON([
            'data' => $players,
        ]);
    }

    public function saveList(): ResponseInterface
    {
        $players = $this->request->getJSON(true);
        $player_ids = [];
        foreach ($players as $player) {
            $server_id = $this->players->insert([
                'app_id' => $player['id'],
                'name' => $player['name'],
                'organization' => $player['organization'] ?? null,
                'phone' => $player['phone'] ?? null,
                'email' => $player['email'] ?? null,
                'address' => $player['address'] ?? null,
                'guest_of_player_id' => $player['guest_of_player_id'] ?? null,
                'status' => $player['status'],
                'total_contribution' => $player['total_contribution'] ?? '0',
                'total_expense' => $player['total_expense'] ?? '0',
                'created_at' => $player['created_at'],
                'updated_at' => $player['updated_at'],
            ]);
            $player_ids[] = [
                'id' => $player['id'],
                'server_id' => $server_id
            ];
        }
        return $this->response->setJSON([
            'data' => $player_ids
        ]);
    }
}
