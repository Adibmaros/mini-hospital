<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $host = Env::get('DB_HOST', 'localhost');
            $db   = Env::get('DB_NAME', 'db_rekam_medis');
            $user = Env::get('DB_USER', 'rekam_medis');
            $pass = Env::get('DB_PASS', 'rekam_medis_pass');
            $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new PDOException("Database connection error: " . $e->getMessage(), (int)$e->getCode());
            }
        }
        return self::$pdo;
    }
}
