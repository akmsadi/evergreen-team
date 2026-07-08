<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Users extends BaseController
{
    private AdminModel $users;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->users = new AdminModel();
    }

    public function login(): ResponseInterface
    {
        $body = $this->request->getJSON(true);

        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        if ($username === '' || $password === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'message' => 'Username and password are required.',
                ]);
        }

        $user = $this->users
            ->where('username', $username)
            ->first();

        if (!$user) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'message' => 'Invalid username or password.',
                ]);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'message' => 'Invalid username or password.',
                ]);
        }

        if ((int) $user['is_active'] !== 1) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'message' => 'Your account is inactive.',
                ]);
        }

        return $this->response->setJSON([
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'token' => $user['password'],
            ],
        ]);
    }
}