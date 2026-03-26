<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;

final class Database
{
    public static function createPdoFromEnv(): PDO
    {
        $host = self::env('DB_HOST', 'db');
        $port = self::env('DB_PORT', '3306');
        $databaseName = self::env('DB_DATABASE', 'app');
        $username = self::env('DB_USERNAME', 'app');
        $password = self::env('DB_PASSWORD', 'app');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $databaseName);

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    private static function env(string $name, string $defaultValue): string
    {
        $value = getenv($name);
        if ($value === false || $value === '') {
            return $defaultValue;
        }
        return $value;
    }
}

