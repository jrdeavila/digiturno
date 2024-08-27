<?php

namespace App\Exceptions;

use Exception;

class AttendantAlreadyBusyException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'attendant_already_busy',
            'Attendant is already busy',
            400
        );
    }
}
