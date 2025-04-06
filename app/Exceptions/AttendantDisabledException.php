<?php

namespace App\Exceptions;

use Exception;

class AttendantDisabledException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'attendant_disabled',
            'Attendant is disabled',
            403
        );
    }
}
