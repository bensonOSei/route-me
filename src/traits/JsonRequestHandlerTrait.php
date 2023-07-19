<?php
declare(strict_types=1);

namespace Benson\RouteMe\Traits;

trait JsonRequestHandlerTrait
{
    /**
     * Send a JSON response with the specified data and status code.
     *
     * @param mixed $data The response data.
     * @param int $statusCode The HTTP status code.
     * @return void
     */
    public function jsonSend($data, int $statusCode = 200, $exit = false): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);

        if ($exit) {
            exit;
        }
        
    }

    /**
     * Get the JSON payload from the request body.
     *
     * @return array|null The JSON payload as an associative array, or null if parsing fails.
     */
    protected function jsonGet(): ?array
    {
        // Get the JSON payload from the request body
        $jsonPayload = file_get_contents('php://input');
        $data = json_decode($jsonPayload, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        return $this->jsonSend([
            'error' => json_last_error_msg(),
            'message' => 'Please check your JSON payload and try again.'
        ], 400, true);
    }
}
