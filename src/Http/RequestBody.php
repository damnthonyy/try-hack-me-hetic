<?php

declare(strict_types=1);

namespace App\Http;

final class RequestBody
{
    /**
     * @return array<string, mixed>
     */
    public static function parse(): array
    {
        $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw !== false && $raw !== '' ? $raw : '[]', true);

            return is_array($data) ? $data : [];
        }

        return $_POST;
    }

    public static function wantsJsonResponse(): bool
    {
        $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));

        return str_contains($contentType, 'application/json');
    }
}
