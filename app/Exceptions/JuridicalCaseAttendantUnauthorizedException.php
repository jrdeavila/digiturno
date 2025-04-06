<?php

namespace App\Exceptions;

use Exception;

class JuridicalCaseAttendantUnauthorizedException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            "juridical_case_attendant_unauthorized",
            "The attendant is not authorized to access this juridical case.",
            403
        );
    }
}
