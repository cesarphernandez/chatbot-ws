<?php

namespace src\Router;
class Router
{
    private $routes = [];

    public function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch($method, $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                $params = $this->extractParams($route['path'], $path);
                $controllerClass = $route['handler'][0];
                $methodName = $route['handler'][1];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }

    private function matchPath($routePath, $requestPath)
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        if (count($routeParts) !== count($requestParts)) return false;
        foreach ($routeParts as $index => $routePart) {
            if ($routePart !== $requestParts[$index] && !preg_match('/^{.+}$/', $routePart)) {
                return false;
            }
        }
        return true;
    }

    private function extractParams($routePath, $requestPath)
    {
        $params = [];
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        foreach ($routeParts as $index => $routePart) {
            if (preg_match('/^{(.+)}$/', $routePart, $matches)) {
                $params[] = $requestParts[$index];
            }
        }
        return $params;
    }
}