<?php
declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    public static function register(string $baseDir): void
    {
        spl_autoload_register(function ($class) use ($baseDir) {
            $prefix = 'App\\';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relativeClass = substr($class, $len);
            $file = $baseDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require $file;
            }
        });
    }
}
