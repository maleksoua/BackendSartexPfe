<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UnableToSaveFileException extends Exception
{
    /**
     * MediaIdsNotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 422, Throwable $previous = null)
    {
        if ($message === '') {
            $message = __('file.unable_to_save');
        }

        parent::__construct($message, $code, $previous);
    }
}
