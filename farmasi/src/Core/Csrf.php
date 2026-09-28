<?php
declare(strict_types=1);

namespace App\Core;

class Csrf
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function getToken(): string
    {
        self::init();
        return $_SESSION['csrf_token'];
    }

    public static function verify(): void
    {
        self::init();
        $token = $_POST['csrf_token'] ?? Request::getHeader('X-CSRF-TOKEN') ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo "403 - Invalid CSRF Token";
            exit;
        }
    }

    public static function input(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
