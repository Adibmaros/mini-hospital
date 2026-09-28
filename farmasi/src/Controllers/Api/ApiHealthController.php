<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Response;

class ApiHealthController
{
    public function health(): void
    {
        Response::json([
            'status' => 'success',
            'message' => 'Sistem Farmasi operational',
            'timestamp' => date('c')
        ]);
    }
}
