<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Http/JsonResponse.php';
require_once __DIR__ . '/../src/Http/Router.php';
require_once __DIR__ . '/../src/Infrastructure/Database.php';
require_once __DIR__ . '/../src/Controllers/HealthController.php';

use App\Controllers\HealthController;
use App\Http\JsonResponse;
use App\Http\Router;
use App\Infrastructure\Database;

try {
    $router = new Router();

    $router->get('/', static function (): void {
        JsonResponse::ok(['service' => 'api']);
    });

    $healthController = new HealthController();
    $router->get('/health', static function () use ($healthController): void {
        $healthController->get();
    });

    $router->get('/db/ping', static function (): void {
        try {
            $pdo = Database::createPdoFromEnv();
            $pdo->query('SELECT 1');
            JsonResponse::ok(['db' => 'ok']);
        } catch (Throwable $throwable) {
            JsonResponse::error('Database unavailable', 500);
        }
    });

    $router->dispatch();
} catch (Throwable $throwable) {
    JsonResponse::error('Internal Server Error', 500);
}

