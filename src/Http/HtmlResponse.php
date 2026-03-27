<?php

declare(strict_types=1);

namespace App\Http;

final class HtmlResponse
{
    public static function viewsRoot(): string
    {
        return dirname(__DIR__, 2) . '/frontend/views';
    }

    public static function render(string $view, array $data = [], int $statusCode = 200): void
    {
        $viewPath = self::resolveViewPath($view);
        $layoutPath = self::viewsRoot() . '/layout.php';

        if (!is_file($layoutPath)) {
            throw new \RuntimeException('Layout missing: frontend/views/layout.php');
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        http_response_code($statusCode);
        header('Content-Type: text/html; charset=utf-8');

        include $layoutPath;
    }

    private static function resolveViewPath(string $view): string
    {
        $view = str_replace(["\0", '\\'], '', $view);
        $view = trim($view, '/');
        if ($view === '' || str_contains($view, '..')) {
            throw new \InvalidArgumentException('Invalid view name');
        }

        if (!str_ends_with($view, '.php')) {
            $view .= '.php';
        }

        $full = self::viewsRoot() . '/' . $view;
        $resolved = realpath($full);
        $rootResolved = realpath(self::viewsRoot());

        if ($resolved === false || $rootResolved === false || !str_starts_with($resolved, $rootResolved)) {
            throw new \InvalidArgumentException('View not found: ' . $view);
        }

        return $resolved;
    }
}
