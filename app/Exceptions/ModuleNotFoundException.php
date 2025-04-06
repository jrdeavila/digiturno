<?php

namespace App\Exceptions;

use Exception;

class ModuleNotFoundException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'module_not_found',
            'Module not found',
            404,
        );
    }
}
