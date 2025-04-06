<?php

namespace App\Exceptions;

use Exception;

class HttpJSONException extends Exception
{
    public string $help;
    public string $status;

    public function __construct(string $message, string $help, string $status)
    {
        $this->message = $message;
        $this->help = $help;
        $this->status = $status;
    }
}
