<?php

declare(strict_types=1);

namespace App\Router;

use App\Controllers\ViewController;
use App\Http\Request;
use App\Middleware\Middleware;
use Exception;

class Router
{
    /** @var array<array{method: string, path: string, handler: callable, middleware: array}> */
    private array $routes = [];

    /** @var string|null */
    private ?string $groupPrefix = null;

    /** @var array */
    private array $groupMiddleware = [];

    /**
     * Add a route to the router.
     *
     * @param string $method
     * @param string $path
     * @param callable|array $handler
     * @param array $middleware
     */
    public function addRoute(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        // Prepend group prefix to the path if a group is active
        if ($this->groupPrefix) {
            $path = rtrim($this->groupPrefix, '/') . '/' . ltrim($path, '/');
        }

        // Merge group middleware with route-specific middleware
        $middleware = array_merge($this->groupMiddleware, $middleware);

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Define a group of routes that share a common prefix and middleware.
     *
     * @param string $prefix
     * @param array $middleware
     * @param callable $callback
     */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $this->groupPrefix = $prefix;
        $this->groupMiddleware = $middleware;

        // Execute the callback to add routes to the group
        $callback($this);

        // Reset the group after adding routes
        $this->groupPrefix = null;
        $this->groupMiddleware = [];
    }

    /**
     * Dispatch the request to the appropriate route.
     *
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

                // Create the handler wrapped with middleware
                $handler = $this->createHandlerWithMiddleware($route['handler'], $route['middleware']);

                // Call the handler
                echo call_user_func_array($handler, array_merge([$request], $params));
                return;
            }
        }

        $this->handleNotFound();
    }

    /**
     * Match the request path to a defined route path.
     *
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
     * Extract parameters from the route path.
     *
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
     * Create the route handler wrapped with middleware.
     *
     * @param callable|array $handler
     * @param array $middleware
     * @return callable
     * @throws Exception
     */
    private function createHandlerWithMiddleware(callable|array $handler, array $middleware): callable
    {
        return array_reduce(
            array_reverse($middleware),
            fn($next, Middleware $mw) => fn(Request $request) => $mw->handle($request, $next),
            fn(Request $request) => $this->callHandler($handler, [$request])
        );
    }

    /**
     * Call the route handler.
     *
     * @param callable|array $handler
     * @param array $params
     * @return string
     * @throws Exception
     */
    private function callHandler(callable|array $handler, array $params): string
    {
        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $methodName] = $handler;
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller class $controllerClass not found");
            }
            $controller = new $controllerClass();
            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method $methodName not found in controller $controllerClass");
            }
            return call_user_func_array([$controller, $methodName], $params);
        }

        return call_user_func_array($handler, $params);
    }

    /**
     * Handle a 404 Not Found response.
     */
    private function handleNotFound(): void
    {
        echo ViewController::notFound();
    }
}
