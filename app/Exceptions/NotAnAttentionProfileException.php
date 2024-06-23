<?php

namespace App\Exceptions;

use Exception;

class NotAnAttentionProfileException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct('not-an-attention-profile', 'This is not an attention profile', 400);
    }
}
