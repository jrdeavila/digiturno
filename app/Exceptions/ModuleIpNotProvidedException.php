<?php

namespace App\Exceptions;

use Exception;

class ModuleIpNotProvidedException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'module_ip_not_provided',
            'Module IP not provided',
            400
        );
    }
}
