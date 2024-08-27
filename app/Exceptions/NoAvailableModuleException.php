<?php

namespace App\Exceptions;

use Exception;

class NoAvailableModuleException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'no_available_module',
            'No available module',
            400
        );
    }
}
