<?php

namespace App\Exceptions;

use Exception;

class AttentionProfileNotFoundException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'attention-profile-not-found',
            'Already don\'t have an attention profile with the services selected',
            404
        );
    }
}
