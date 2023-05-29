# benson/my-router

## Description

Route-Me is a lightweight and flexible PHP router package for creating RESTful APIs. It supports route parameters, middleware, and PSR-7 request/response handling. Route-Me is built using the [PSR-7 HTTP message interfaces](https://www.php-fig.org/psr/psr-7/) and can be used with any PSR-7 implementation. It is also framework-agnostic and can be used with any PHP framework or standalone application. Route-Me is developed and maintained by [Benson Osei-Mensah](https://github/bensonOSei).

## Installation

To install Route-Me, run the following command in your project directory:

```bash
composer require benson/my-router
```

## Table of Contents

- [Features](#features)
- [Usage](#usage)
- [Contribution](#contribution)
- [License](#license)
- [Author](#author)

## Features

- Route Definition: Add routes to the router using the `addRoute()` method.
- Middleware: Apply middleware functions to specific routes or globally using the `withMiddleware()` method.
- Request Handling: Handle incoming requests by matching URL and HTTP method against defined routes.
- Error Handling: Handle 404 Not Found scenarios and custom error handling logic.
- Route Parameters: Support for route parameters defined within the URL pattern.
- Flexible Callbacks: Use closures or class methods as callback functions.
- PSR-7 Support: Built-in support for JSON request/response handling.

## Usage

To use Route-Me in your project, follow these steps:

1. Import the composer autoloader into your PHP file:

    ```php
    require_once 'vendor/autoload.php';
    ```

2. Create a new instance of the Router class:

    ```php
    $router = new Router();
    ```

3. Add routes to the router using the `addRoute()` method:

    ```php
    $router->addRoute('GET', '/users', function () {
        // Handle GET request to /users
    });

    // Add class method as callback
    $router->addRoute('GET', '/users', "App\Controllers\UserController@index");

    // Add route parameters
    $router->addRoute('GET', '/users/{id}', function ($id) {
        // Handle GET request to /users/{id}
    });

    // Add middleware
    $router->addRoute(
        'GET', // HTTP method
        '/user/{id}', // URL pattern
        'App\Controllers\UserController@show', // Callback function
        'App\Middleware\Authenticate@handle' // Middleware function
        );

        // Add middleware to all routes
        $router->withMiddleware('App\Middleware\Authenticate@handle');

    $router->addRoute('POST', '/users', function () {
        // Handle POST request to /users
    });
    ```

4. Handle incoming requests by calling the `handleRequest()` method:

    ```php
    $router->handleRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); 
    ```

<!-- ## API Documentation

For detailed information on the available routes, request methods, and request/response formats, please refer to our [API documentation](link-to-api-documentation). -->

## Contribution

Contributions are welcome! To contribute to Route-Me, follow these steps:

1. Fork the repository.
2. Create a new branch.

    ``` bash
    git checkout -b your-branch-name
    ```

3. Make your changes.
4. Commit your changes and push them to your branch. Make sure to add a clear commit message and include any relevant documentation updates. Since Route-Me follows the [PSR-2 coding standards](https://www.php-fig.org/psr/psr-2/), please ensure that your code adheres to these standards. If you are not sure, you can use the [PHP Coding Standards Fixer](https://cs.symfony.com/)

    ``` bash
    git add .
    git commit -m "your commit message"
    git push origin your-branch-name
    ```

5. Submit a pull request.
Please ensure that you adhere to the coding standards and write clear commit messages.

## License

Route-Me is open-source software released under the MIT License. See [LICENSE](./LICENSE) for details.

## Author

Route-Me is developed and maintained by [Benson Ose-Mensah](https://github.com/bensonOSei).
