<?php

namespace App\Exceptions;

use Exception;

class ShiftInProgressDeletingFailedException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'shift_in_progress_cannot_be_deleted',
            'Shift in progress cannot be deleted',
            400
        );
    }
}
