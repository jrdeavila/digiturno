<?php

namespace App\Exceptions;

use Exception;

class AttendantNotAbsentException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct('attendant_not_absent', 'Attendant is not absent', 400);
    }
}
