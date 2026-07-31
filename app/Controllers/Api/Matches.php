<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MatchModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Matches extends BaseController
{
    private MatchModel $matches;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->matches = new MatchModel();
    }

    public function index(): ResponseInterface
    {
        return $this->response->setJSON([
            'data' => $this->matches->orderedList(),
        ]);
    }

    public function scheduled(): ResponseInterface
    {
        return $this->response->setJSON([
            'data' => $this->matches->scheduledList(),
        ]);
    }
}
