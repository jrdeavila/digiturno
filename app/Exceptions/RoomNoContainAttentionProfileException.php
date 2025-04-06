<?php

namespace App\Exceptions;

use Exception;

class RoomNoContainAttentionProfileException extends HttpJSONException
{
    public function __construct()
    {
        parent::__construct(
            'room_no_contain_attention_profile',
            'The room no contain the attention profile selected',
            400,
        );
    }
}
