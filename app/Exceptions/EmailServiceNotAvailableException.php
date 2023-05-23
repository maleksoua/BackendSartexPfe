<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class EmailServiceNotAvailableException extends Exception
{
    /**
     * MediaTypeNotCompatibleWithModuleException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 503, Throwable $previous = null)
    {
        if ($message === '') {
            $message = __('mail_service_not_available');
        }

        parent::__construct($message, $code, $previous);
    }

}
