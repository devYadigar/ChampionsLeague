<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Exception|null $previous
     */
    public function __construct($message = "Application Exception", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
