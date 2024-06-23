<?php

namespace App\Exceptions;

use Exception;

class NotAServiceException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct('not-a-service', 'This is not a service', 400);
    }
}
