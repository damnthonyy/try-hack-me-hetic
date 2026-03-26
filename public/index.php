<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/Http/JsonResponse.php';
require_once __DIR__ . '/../src/Http/Router.php';
require_once __DIR__ . '/../src/Infrastructure/Database.php';

require_once __DIR__ . '/../src/Controllers/HealthController.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';
require_once __DIR__ . '/../src/Controllers/FileController.php';
require_once __DIR__ . '/../src/Controllers/FlagController.php';

use App\Http\JsonResponse;
use App\Http\Router;
use App\Infrastructure\Database;

use App\Controllers\HealthController;
use App\Controllers\UserController;
use App\Controllers\FileController;
use App\Controllers\FlagController;

try {
    $router = new Router();

    // Controllers
    $healthController = new HealthController();
    $userController = new UserController();
    $fileController = new FileController();
    $flagController = new FlagController();

    // Root
    $router->get('/', static function (): void {
        JsonResponse::ok(['service' => 'api']);
    });

    // Health
    $router->get('/health', static function () use ($healthController): void {
        $healthController->get();
    });

    // DB test
    $router->get('/db/ping', static function (): void {
        try {
            $pdo = Database::getConnection();
            $pdo->query('SELECT 1');
            JsonResponse::ok(['db' => 'ok']);
        } catch (\Throwable $e) {
            JsonResponse::error('Database unavailable', 500);
        }
    });

    // USERS (SQL Injection)
    $router->get('/users', static function () use ($userController): void {
        $userController->getUser();
    });

    // FILES
    $router->get('/upload', static function () use ($fileController): void {
        $fileController->upload();
    });

    $router->get('/download', static function () use ($fileController): void {
        $fileController->download();
    });

    // FLAG
    $router->get('/flag', static function () use ($flagController): void {
        $flagController->getFlag();
    });

    $router->dispatch();

} catch (\Throwable $e) {
    echo $e->getMessage();
}