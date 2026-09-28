<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Core/Autoloader.php';

use App\Core\Autoloader;
use App\Core\ErrorHandler;
use App\Core\Router;
use App\Core\Request;
use App\Core\ApiAuth;

Autoloader::register(__DIR__ . '/../src');
ErrorHandler::register();

$router = new Router();

// API Routes
$router->get('/api/health', [\App\Controllers\Api\ApiHealthController::class, 'health']);

// Web Routes
$router->get('/', [\App\Controllers\Web\HomeController::class, 'index']);
$router->get('/antrean', [\App\Controllers\Web\AntreanController::class, 'index']);

$router->get('/pemeriksaan/{id_kunjungan}', [\App\Controllers\Web\PemeriksaanController::class, 'create']);
$router->post('/pemeriksaan/store', [\App\Controllers\Web\PemeriksaanController::class, 'store']);
$router->post('/pemeriksaan/resend/{id_rm}', [\App\Controllers\Web\PemeriksaanController::class, 'resendResep']);

$router->get('/riwayat', [\App\Controllers\Web\RiwayatController::class, 'index']);
$router->get('/riwayat/{id_pasien}', [\App\Controllers\Web\RiwayatController::class, 'show']);

$router->dispatch(Request::getMethod(), Request::getUri());
