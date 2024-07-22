<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'invalid token',
            'The token is invalid',
            401
        );
    }
}
