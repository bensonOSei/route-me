<?php

namespace Benson\RouteMe\Handlers;

class JsonHandler
{
    public static function send($payload)
    {
        return json_encode($payload);
    }


    /**
     * Respond to the request
     * 
     * @param string $payload The response payload.
     * @return void
     */
    public static function respond($payload)
    {
        echo json_encode($payload);
    }


    /**
     * Receive the request payload
     * 
     * @param string $payload The request payload.
     * @return object Returns the request payload as an object.
     */
    public static function receive($payload)
    {
        return json_decode($payload, true);
    }


    /**
     * Receive the request payload as an array
     * 
     * @param string $payload The request payload.
     * @return array Returns the request payload as an array.
     */
    public static function receiveAsArray($payload)
    {
        return json_decode($payload, true);
    }

    
    /**
     * Check if the content type is application/json
     * 
     * @return bool Returns true if the content type is application/json
     *              and false otherwise.
     */
    public static function isJsonContent()
    {
        return $_SERVER['CONTENT_TYPE'] === 'application/json' ? true : false;
    }
}
