<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'invalid_credentials',
            'Invalid credentials',
            401
        );
    }
}
