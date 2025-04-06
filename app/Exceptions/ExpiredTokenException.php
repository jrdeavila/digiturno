<?php

namespace App\Exceptions;

use Exception;

class ExpiredTokenException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'expired_token',
            'The token has expired',
            401
        );
    }
}
