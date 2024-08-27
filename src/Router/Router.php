<?php

declare(strict_types=1);

namespace App\Router;

use App\Http\Request;
use Exception;

class Router
{
    /** @var array<array{method: string, path: string, handler: callable}> */
    private array $routes = [];

    /**
     * @param string $method
     * @param string $path
     * @param callable|array $handler
     */
    public function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $path = parse_url($request->getUri(), PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $params = $this->extractParams($route['path'], $path);
                $this->callHandler($route['handler'], array_merge([$request], $params));
                return;
            }
        }

        $this->handleNotFound();
    }

    /**
     * @param string $routePath
     * @param string $requestPath
     * @return bool
     */
    private function matchPath(string $routePath, string $requestPath): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        foreach ($routeParts as $index => $routePart) {
            if ($routePart !== $requestParts[$index] && !preg_match('/^{.+}$/', $routePart)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $routePath
     * @param string $requestPath
     * @return array
     */
    private function extractParams(string $routePath, string $requestPath): array
    {
        $params = [];
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        foreach ($routeParts as $index => $routePart) {
            if (preg_match('/^{(.+)}$/', $routePart)) {
                $params[] = $requestParts[$index];
            }
        }

        return $params;
    }

    /**
     * @param callable|array{class-string, string} $handler
     * @param array $params
     * @throws Exception
     */
    private function callHandler(callable|array $handler, array $params): void
    {
        $response = null;

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $methodName] = $handler;
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller class $controllerClass not found");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName not found in controller $controllerClass");
            }
            $response = call_user_func_array([$controller, $methodName], $params);
        } else {
            $response = call_user_func_array($handler, $params);
        }

        if ($response !== null) {
            echo $response;
        }
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}