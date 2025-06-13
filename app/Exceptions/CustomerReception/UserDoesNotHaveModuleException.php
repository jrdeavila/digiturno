<?php

namespace App\Exceptions\CustomerReception;

use Exception;
use Throwable;

class UserDoesNotHaveModuleException extends Exception
{
    public function __construct($message = "", $code = 500, ?Throwable $previous = null)
    {
        $message = "El usuario no tiene un modulo asignado";
        parent::__construct($message, $code, $previous);
    }
}
