<?php

declare(strict_types=1);

namespace App\Http;

final class JsonResponse
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function ok(array $payload, int $statusCode = 200): void
    {
        self::send($payload, $statusCode);
    }

    public static function error(string $message, int $statusCode): void
    {
        self::send(['error' => ['message' => $message]], $statusCode);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function send(array $payload, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

