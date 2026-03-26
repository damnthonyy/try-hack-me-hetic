<?php

declare(strict_types=1);

namespace App\Http;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 86400);
            
            ini_set('session.cookie_httponly', '1'); 
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.cookie_lifetime', (string)$lifetime);
            ini_set('session.gc_maxlifetime', (string)$lifetime);

            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
                ini_set('session.cookie_secure', '1');
            }
            
            session_start();
        }
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }
}
