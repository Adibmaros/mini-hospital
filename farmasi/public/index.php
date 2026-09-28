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
$router->post('/api/resep', [\App\Controllers\Api\ApiResepController::class, 'store'], [ApiAuth::class]);
$router->get('/api/resep', [\App\Controllers\Api\ApiResepController::class, 'index'], [ApiAuth::class]);
$router->get('/api/resep/{id}', [\App\Controllers\Api\ApiResepController::class, 'show'], [ApiAuth::class]);
$router->patch('/api/resep/{id}/status', [\App\Controllers\Api\ApiResepController::class, 'updateStatus'], [ApiAuth::class]);
$router->get('/api/obat', [\App\Controllers\Api\ApiObatController::class, 'index'], [ApiAuth::class]);

// Web Routes
$router->get('/', [\App\Controllers\Web\HomeController::class, 'index']);
$router->get('/obat', [\App\Controllers\Web\ObatController::class, 'index']);
$router->get('/obat/create', [\App\Controllers\Web\ObatController::class, 'create']);
$router->post('/obat/store', [\App\Controllers\Web\ObatController::class, 'store']);
$router->get('/obat/{id}/edit', [\App\Controllers\Web\ObatController::class, 'edit']);
$router->post('/obat/{id}/update', [\App\Controllers\Web\ObatController::class, 'update']);

$router->get('/resep', [\App\Controllers\Web\ResepController::class, 'index']);
$router->get('/resep/{id_rm}', [\App\Controllers\Web\ResepController::class, 'show']);
$router->post('/resep/{id_resep}/status', [\App\Controllers\Web\ResepController::class, 'updateItemStatus']);

$router->dispatch(Request::getMethod(), Request::getUri());
