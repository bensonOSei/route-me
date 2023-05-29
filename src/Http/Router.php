<?php

declare(strict_types=1);

namespace Benson\RouteMe\Http;

use Benson\RouteMe\Traits\JsonRequestHandlerTrait;

class Router
{
    use JsonRequestHandlerTrait;
    private $routes = [];
    private $middlewares = [];

    /**
     * Add a route to the router.
     *
     * @param string   $method   The HTTP method (GET, POST, etc.).
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return void
     */
    public function addRoute(string $method, string $pattern, $callback, array $middlewares = [])
    {

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Handle an incoming request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $url    The URL of the request.
     * @return void 
     */
    public function handleRequest(string $method, string $url): void
    {
        foreach ($this->routes as $route) {
            $params = []; // Initialize $params array

            if ($route['method'] === $method && $this->matchRoutePattern($route['pattern'], $url, $params)) {
                try {

                    if (count($route['middlewares']) > 0) {
                        $this->applyMiddleware($route['middlewares']);
                    }
                    // Call the route callback with the matched parameters
                    if (is_string($route['callback']) && strpos($route['callback'], '@') !== false) {
                        [$class, $method] = explode('@', $route['callback']);
                        $callbackInstance = new $class();
                        call_user_func([$callbackInstance, $method], ...array_values($params));
                    } else {
                        call_user_func($route['callback'], ...array_values($params));
                    }

                    return;
                } catch (\Throwable $e) {
                    $this->handleError($e);
                    return;
                }
            }
        }

        // No matching route found, handle 404 Not Found
        $this->handleNotFound();
    }

    /**
     * Handle a 404 Not Found scenario.
     *
     * @return void
     */
    public function handleNotFound(): void
    {
        // Custom logic for handling 404 Not Found
        header('HTTP/1.0 404 Not Found');
        echo "404 Not Found";
    }

    /**
     * Handle an error scenario.
     *
     * @param \Throwable $e The exception object.
     * @return void
     */
    public function handleError(\Throwable $e): void
    {
        // Custom logic for handling errors
        // You can log the error, display a friendly error page, etc.
        // Example: log the error message
        error_log($e->getMessage());

        // Send an appropriate HTTP response
        header('HTTP/1.1 500 Internal Server Error');
        echo $this->jsonSend([
            'error' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ], 500);
    }

    /**
     * Add middleware to the router.
     *
     * @param callable $middleware The middleware callback function.
     * @return self
     */
    public function withMiddleware($middleware): self
    {
        $this->middlewares[] = $middleware;
        $this->applyMiddleware($this->middlewares);
        return $this;
    }



    /**
     * Apply the registered middleware functions.
     *
     * @return void
     */
    private function applyMiddleware(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware) && strpos($middleware, '@') !== false) {
                [$class, $method] = explode('@', $middleware);
                $middlewareInstance = new $class();
                call_user_func([$middlewareInstance, $method]);
            } else {
                call_user_func($middleware);
            }
        }
    }

    /**
     * Match the route pattern against the URL.
     *
     * @param string $pattern The route pattern to match.
     * @param string $url     The URL to match against.
     * @param array  $params  The matched route parameters (output).
     * @return bool True if the pattern matches, false otherwise.
     */
    private function matchRoutePattern(string $pattern, string $url, array &$params): bool
    {
        $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
        $pattern = preg_replace('/\{(.+?)\}/', '(?<$1>[^\/]+)', $pattern);
        return preg_match($pattern, $url, $matches) && $this->extractRouteParams($matches, $params);
    }

    private function extractRouteParams(array $matches, array &$params): bool
    {
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }
        return true;
    }
}
