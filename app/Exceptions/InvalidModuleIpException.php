<?php

namespace App\Exceptions;

use Exception;

class InvalidModuleIpException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'invalid_module_ip',
            'Invalid module IP',
            400,
        );
    }
}
