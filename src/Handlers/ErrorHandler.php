<?php

namespace Benson\RouteMe\Handlers;

class ErrorHandler
{
    public static function handle(\Throwable $e, $code = 500): void
    {
        // Custom logic for handling errors
        // You can log the error, display a friendly error page, etc.
        // Example: log the error message
        error_log($e->getMessage());

        // Send an appropriate HTTP response
        http_response_code($code);
        echo JsonHandler::send([
            'error' => [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ]);
    }

    public static function send($payload)
    {
        return JsonHandler::send([
            'error' => $payload
        ]);
    }
}
