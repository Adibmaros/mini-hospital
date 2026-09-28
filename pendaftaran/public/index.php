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
$router->get('/api/kunjungan', [\App\Controllers\Api\ApiKunjunganController::class, 'index'], [ApiAuth::class]);
$router->get('/api/kunjungan/{id}', [\App\Controllers\Api\ApiKunjunganController::class, 'show'], [ApiAuth::class]);
$router->patch('/api/kunjungan/{id}/status', [\App\Controllers\Api\ApiKunjunganController::class, 'updateStatus'], [ApiAuth::class]);
$router->get('/api/pasien/{id}', [\App\Controllers\Api\ApiPasienController::class, 'show'], [ApiAuth::class]);

// Web Routes
$router->get('/', [\App\Controllers\Web\HomeController::class, 'index']);
$router->get('/pasien', [\App\Controllers\Web\PasienController::class, 'index']);
$router->get('/pasien/create', [\App\Controllers\Web\PasienController::class, 'create']);
$router->post('/pasien/store', [\App\Controllers\Web\PasienController::class, 'store']);
$router->get('/pasien/{id}/edit', [\App\Controllers\Web\PasienController::class, 'edit']);
$router->post('/pasien/{id}/update', [\App\Controllers\Web\PasienController::class, 'update']);

$router->get('/kunjungan', [\App\Controllers\Web\KunjunganController::class, 'index']);
$router->get('/kunjungan/create', [\App\Controllers\Web\KunjunganController::class, 'create']);
$router->post('/kunjungan/store', [\App\Controllers\Web\KunjunganController::class, 'store']);

$router->dispatch(Request::getMethod(), Request::getUri());
