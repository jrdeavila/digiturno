<?php

namespace App\Exceptions;

use Exception;

class ShiftDontHaveModuleAssignedException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct('shift-dont-have-module-assigned', 'This shift don\'t have a module assigned', 400);
    }
}
