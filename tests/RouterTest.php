<?php

declare(strict_types=1);

namespace Tests;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testAddRouteWithoutMiddleware()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->addRoute('GET', '/', function () {
            return 'Hello World';
        });

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => []
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }

    public function testAddRouteWithMiddleware()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->addRoute('GET', '/', function () {
            return 'Hello World';
        }, ['auth']);

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }

    public function testAddRouteWithMultipleMiddleware()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->addRoute('GET', '/', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }


    public function testAddRouteWithMultipleRoutes()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->addRoute('GET', '/', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $router->addRoute('POST', '/users', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ],
            [
                'method' => 'POST',
                'pattern' => '/users',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }


    public function testAddRouteWithMultipleRoutesAndMiddleware()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->addRoute('GET', '/', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $router->addRoute('POST', '/users', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $router->addRoute('POST', '/users', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ],
            [
                'method' => 'POST',
                'pattern' => '/users',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ],
            [
                'method' => 'POST',
                'pattern' => '/users',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }

    public function testPostRoute()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->post('/users', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $expected = [
            [
                'method' => 'POST',
                'pattern' => '/users',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }

    public function testGetRoute()
    {
        // given we have a router
        $router = new \Benson\RouteMe\Http\Router();

        // when we add a route
        $router->get('/users', function () {
            return 'Hello World';
        }, ['auth', 'admin']);

        $expected = [
            [
                'method' => 'GET',
                'pattern' => '/users',
                'callback' => function () {
                    return 'Hello World';
                },
                'middleware' => ['auth', 'admin']
            ]
        ];

        // then the route should be added
        $this->assertEquals($expected, $router->getRoutes());

    }

}