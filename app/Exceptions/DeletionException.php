<?php

namespace App\Exceptions;


use Throwable;

class DeletionException extends \Exception
{
    public function __construct(string $message = "Unable to delete Entity", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
