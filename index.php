<?php
use Benson\RouteMe\Http\Router;

require_once './vendor/autoload.php';

$router = new Router();

$router->post('/users', function () {
    return 'Hello World';
});

$router->run(); 
