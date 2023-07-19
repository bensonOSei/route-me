<?php

declare(strict_types=1);

namespace Benson\RouteMe\Http;

use Benson\RouteMe\Handlers\ErrorHandler;
use Benson\RouteMe\Handlers\JsonHandler;
use Benson\RouteMe\Traits\JsonRequestHandlerTrait;
use Closure;

/**
 * The Router class. This class handles the routing of the application.
 * 
 * @package Benson\RouteMe\Http
 * @since 1.0.0
 */
class Router
{
    use JsonRequestHandlerTrait;
    private $routes = [];
    private $middleware = [];

    public function __construct()
    {
        header('Content-type: application/json');
    }

    /**
     * Add a route to the router.
     *
     * @param string   $method   The HTTP method (GET, POST, etc.).
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function addRoute(string $method, string $pattern, $callback, array $middleware = []): self
    {

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
            'middleware' => $middleware
        ];

        return $this;
    }



    /**
     * Add a route that responds to the GET HTTP method
     * 
     * @param string $pattern The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function get(string $pattern, Closure $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') $this->handleNotFound();

        return $this->addRoute('GET', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the POST HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function post(string $pattern, $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->handleNotFound();

        return $this->addRoute('POST', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the PUT HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function put(string $pattern, $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') $this->handleNotFound();

        return $this->addRoute('PUT', $pattern, $callback, $middleware);
    }



    /**
     * Add a route that responds to the DELETE HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function delete(string $pattern, $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') $this->handleNotFound();

        return $this->addRoute('DELETE', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the PATCH HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function patch(string $pattern, $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') $this->handleNotFound();

        return $this->addRoute('PATCH', $pattern, $callback, $middleware);
    }




    /**
     * Add a route that responds to the OPTIONS HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function options(string $pattern, $callback, array $middleware = [])
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') $this->handleNotFound();

        return $this->addRoute('OPTIONS', $pattern, $callback, $middleware);
    }



    /**
     * Add a route that responds to any HTTP method.
     * 
     * @param string   $pattern  The URL pattern to match.
     * @param callable $callback The callback function to execute for the route.
     * @return self Returns the Router instance.
     */
    public function any(string $pattern, $callback, array $middleware = [])
    {
        // check if the request method is supported
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'])) $this->handleNotFound();

        // get the request method
        $method = $_SERVER['REQUEST_METHOD'];

        return $this->addRoute($method, $pattern, $callback, $middleware);
    }





    /**
     * Handle an incoming request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $url    The URL of the request.
     * @return void 
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route) {
            $params = []; // Initialize $params array

            if ($route['method'] === $method && $this->matchRoutePattern($route['pattern'], $url, $params)) {
                try {

                    if (count($route['middleware']) > 0) {
                        $this->applyMiddleware($route['middleware']);
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
                    ErrorHandler::handle($e);
                    return;
                }
            }
        }

        // No matching route found, handle 404 Not Found
        // $this->handleNotFound();
    }





    /**
     * Handle a 404 Not Found scenario.
     *
     */
    public function handleNotFound()
    {

        JsonHandler::respond(
            '404 Not Found',
        );
    }


    public function getRoutes()
    {
        return $this->routes;
    }





    
    /**
     * Add middleware to the router.
     *
     * @param callable $middleware The middleware callback function.
     * @return self
     */
    public function withMiddleware($middleware): self
    {
        $this->middleware[] = $middleware;
        $this->applyMiddleware($this->middleware);
        return $this;
    }



    /**
     * Apply the registered middleware functions.
     *
     * @return void
     */
    private function applyMiddleware(array $middleware): void
    {
        foreach ($middleware as $middleware) {
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
