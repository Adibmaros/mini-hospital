<?php
declare(strict_types=1);

namespace App\Core;

use Throwable;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(Throwable $e): void
    {
        error_log(sprintf("[%s] %s in %s:%d\n%s", get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));

        $uri = Request::getUri();
        if (str_starts_with($uri, '/api/')) {
            Response::json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        } else {
            http_response_code(500);
            echo "<h1>500 - Internal Server Error</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
