<?php

namespace App\Exceptions;

use Exception;

class ModuleNotActiveException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'module_not_active',
            'Module not active',
            403,
        );
    }
}
