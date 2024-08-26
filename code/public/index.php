<?php

use src\Controllers\UserController;
use src\Router\Router;

require_once __DIR__ . '/../src/Router/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

$router = new Router();
$router->addRoute('GET', '/users', [UserController::class, 'index']);
$router->addRoute('GET', '/users/{id}', [UserController::class, 'show']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);