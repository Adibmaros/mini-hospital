<?php
declare(strict_types=1);

namespace App\Core;

class ApiAuth
{
    public function handle(): void
    {
        $uri = Request::getUri();
        if ($uri === '/api/health') {
            return;
        }

        $expectedKey = Env::get('API_KEY', 'ganti-dengan-kunci-rahasia');
        $apiKey = Request::getHeader('X-API-KEY');

        if (!$apiKey || $apiKey !== $expectedKey) {
            Response::json([
                'status' => 'error',
                'message' => 'Unauthorized: API Key tidak valid atau tidak disertakan'
            ], 401);
        }
    }
}
