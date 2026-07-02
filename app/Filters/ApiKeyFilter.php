<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $providedKey = (string) $request->getHeaderLine('X-Api-Key');
        $expectedKey = (string) env('api.key', '');

        if ($expectedKey === '' || !hash_equals($expectedKey, $providedKey)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Invalid or missing API key.']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
