<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pathPattern, callable|array $handler, array $middleware = []): void
    {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pathPattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pathPattern,
            'regex' => $regex,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function patch(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('PATCH', $path, $handler, $middleware);
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['regex'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    $mwInstance = new $mw();
                    $mwInstance->handle();
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $methodName] = $handler;
                    $controller = new $class();
                    call_user_func_array([$controller, $methodName], [$params]);
                } else {
                    call_user_func_array($handler, [$params]);
                }
                return;
            }
        }

        if (str_starts_with($uri, '/api/')) {
            Response::json([
                'status' => 'error',
                'message' => 'Endpoint tidak ditemukan'
            ], 404);
        } else {
            http_response_code(404);
            echo "404 - Halaman tidak ditemukan";
        }
    }
}
