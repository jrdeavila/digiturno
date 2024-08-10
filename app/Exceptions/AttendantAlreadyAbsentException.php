<?php

namespace App\Exceptions;

use Exception;

class AttendantAlreadyAbsentException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct('attendant_already_absent', 'Attendant is already absent', 400);
    }
}
