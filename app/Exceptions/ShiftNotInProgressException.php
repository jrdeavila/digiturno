<?php

namespace App\Exceptions;

use Exception;

class ShiftNotInProgressException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'shift_not_in_progress',
            'The shift is not in progress.',
            400,
        );
    }
}
