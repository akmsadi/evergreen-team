<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\VenueModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Venues extends BaseController
{
    private VenueModel $venues;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->venues = new VenueModel();
    }

    public function index(): ResponseInterface
    {
        return $this->response->setJSON([
            'data' => $this->venues->orderBy('name', 'ASC')->findAll(),
        ]);
    }
}
