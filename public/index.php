<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controllers\UserController;
use App\Http\Request;
use App\Http\JsonResponse;


/**
 * Bootstrap the application
 *
 * @return Router
 */
function bootstrapApplication(): Router
{
    $router = new Router();

    // Define routes
    $router->addRoute('GET', '/users', [UserController::class, 'index']);
    $router->addRoute('GET', '/users/{id}', [UserController::class, 'show']);
    $router->addRoute('POST', '/users', [UserController::class, 'create']);

    return $router;
}

/**
 * Run the application
 *
 * @param Router $router
 * @return void
 */
function runApplication(Router $router): void
{
    $request = new Request();

    try {
        $router->dispatch($request);
    } catch (Exception $e) {
        handleException($e);
    }
}

/**
 * Handle uncaught exceptions
 *
 * @param Exception $e
 * @return void
 */
function handleException(Exception $e): void
{
    echo JsonResponse::error($e->getMessage(), 500);
}

// Bootstrap and run the application
$router = bootstrapApplication();
runApplication($router);