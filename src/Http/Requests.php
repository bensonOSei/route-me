<?php
// this class intercepts all requests and handles them

namespace Benson\RouteMe\Http;

use Benson\RouteMe\Handlers\ErrorHandler;
use Benson\RouteMe\Handlers\JsonHandler;

class Requests
{

    public function __construct()
    {
        // intercept all requests
        $this->intercept();
    }

    // intercept all requests
    private function intercept()
    {
        // intercept all requests
        $this->interceptAll();
    }

    /**
     * Intercept all requests. This method intercepts all requests and
     * sets the request properties.
     * 
     * @return void
     */
    private function interceptAll()
    {
        $request = [];
        // if content type is application/json
        if (JsonHandler::isJsonContent())
            $request = JsonHandler::receiveAsArray(file_get_contents('php://input'));
        else
            $request = $_REQUEST;


        foreach ($request as $key => $value) {
            $this->{$key} = $value;
        }

        // return $this;
    }




    /**
     * Magic method to get a property
     * 
     * @param string $name The name of the property to get.
     * @return string Returns the value of the property or an error message
     *                if the property does not exist.
     */
    public function __get($name): string
    {
        if (isset($this->{$name}))
            return $this->{$name};

        return ErrorHandler::send([
            'Property ' . $name . ' does not exist'
        ]);
    }
}
