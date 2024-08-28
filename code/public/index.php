<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controllers\WebhookController;
use App\Middleware\VerifyWebhookSignature;
use App\Controllers\UserController;
use App\Controllers\ViewController;
use App\Http\Request;
use App\Http\JsonResponse;
use App\Middleware\AuthMiddleware;

/**
 * Bootstrap the application
 *
 * @return Router
 */
function bootstrapApplication(): Router
{
    $router = new Router();

    $router->addRoute('GET', '/webhook/whatsapp', [WebhookController::class, 'handleWhatsAppWebhook'], [new VerifyWebhookSignature()]);


    $router->group('/users', [new AuthMiddleware()], function (Router $router) {
        $router->addRoute('GET', '/', [UserController::class, 'index']);
        $router->addRoute('GET', '/{id}', [UserController::class, 'show']);
        $router->addRoute('POST', '/', [UserController::class, 'create']);
    });

    //Views
    $router->addRoute('GET', '/presentation', [ViewController::class, 'presentation']);
    $router->addRoute('GET', '/user', [ViewController::class, 'user']);

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
