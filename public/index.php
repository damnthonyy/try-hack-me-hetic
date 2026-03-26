<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Http/HtmlResponse.php';
require_once __DIR__ . '/../src/Http/JsonResponse.php';
require_once __DIR__ . '/../src/Http/Router.php';
require_once __DIR__ . '/../src/Http/Cors.php';
require_once __DIR__ . '/../src/Http/Session.php';
require_once __DIR__ . '/../src/Infrastructure/Database.php';
require_once __DIR__ . '/../src/Controllers/HealthController.php';
require_once __DIR__ . '/../src/Controllers/LoginController.php';

use App\Controllers\HealthController;
use App\Http\HtmlResponse;
use App\Controllers\LoginController;
use App\Http\Cors;
use App\Http\JsonResponse;
use App\Http\Router;
use App\Infrastructure\Database;

Cors::handle();

try {
    $router = new Router();

    $router->get('/', static function (): void {
        JsonResponse::ok(['service' => 'api']);
    });

    $router->get('/api', static function (): void {
        JsonResponse::ok(['service' => 'api']);
    });

    $healthController = new HealthController();
    $router->get('/health', static function () use ($healthController): void {
        $healthController->get();
    });

    $loginController = new LoginController();
    $router->post('/login', static function () use ($loginController): void {
        $loginController->login();
    });

    $router->post('/register', static function () use ($loginController): void {
        $loginController->register();
    });

    $router->post('/logout', static function () use ($loginController): void {
        $loginController->logout();
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

