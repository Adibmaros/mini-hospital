<?php
declare(strict_types=1);

namespace App\Core;

class Env
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return $val;
        }
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}
