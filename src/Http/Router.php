<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /**
     * @var array<string, array<string, callable>>
     */
    private array $routesByMethodAndPath = [];

    public function get(string $path, callable $handler): void
    {
        $this->routesByMethodAndPath['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routesByMethodAndPath['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = $this->normalizePath(parse_url($requestUri, PHP_URL_PATH) ?: '/');

        $handler = $this->routesByMethodAndPath[$method][$path] ?? null;
        if ($handler === null) {
            JsonResponse::error('Not Found', 404);
            return;
        }

        $handler();
    }

    private function normalizePath(string $path): string
    {
        $trimmed = '/' . ltrim($path, '/');
        if ($trimmed !== '/' && str_ends_with($trimmed, '/')) {
            return rtrim($trimmed, '/');
        }
        return $trimmed;
    }
}

