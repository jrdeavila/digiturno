<?php

namespace App\Exceptions;

use Exception;

class ModuleAlreadyInProgressException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'module-already-in-progress',
            'Module is already in progress',
            400
        );
    }
}
