<?php

declare(strict_types=1);

namespace App\Http;

final class Cors
{
    /**
     * Paths that may be called cross-origin (JSON API + auth JSON endpoints).
     */
    private const ALLOWED_PATHS = [
        '/api',
        '/health',
        '/db/ping',
        '/login',
        '/register',
        '/logout',
    ];

    public static function handle(): void
    {
        $path = self::normalizePath(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        if (!in_array($path, self::ALLOWED_PATHS, true)) {
            return;
        }

        self::sendHeaders();

        if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    private static function sendHeaders(): void
    {
        $allowedOrigin = $_ENV['CORS_ORIGIN'] ?? 'http://localhost:3000';

        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }

    private static function normalizePath(string $path): string
    {
        $trimmed = '/' . ltrim($path, '/');
        if ($trimmed !== '/' && str_ends_with($trimmed, '/')) {
            return rtrim($trimmed, '/');
        }

        return $trimmed;
    }
}
