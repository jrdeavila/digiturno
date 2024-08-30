<?php

namespace App\Exceptions;

use Exception;

class ShiftAlreadyAssignedException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'shift_already_assigned',
            'Shift already assigned',
            400
        );
    }
}
