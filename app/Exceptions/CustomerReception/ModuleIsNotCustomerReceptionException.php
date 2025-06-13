<?php

namespace App\Exceptions\CustomerReception;

use Exception;
use Throwable;

class ModuleIsNotCustomerReceptionException extends Exception
{
    public function __construct($message = "", $code = 500, ?Throwable $previous = null)
    {
        $message = "El modulo no es de recepcion de clientes";
        parent::__construct($message, $code, $previous);
    }
}
