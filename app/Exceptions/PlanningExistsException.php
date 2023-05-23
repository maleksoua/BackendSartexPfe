<?php

namespace App\Exceptions;
use Exception;
use Throwable;

class PlanningExistsException extends Exception
{
    /**
     * PlanningExists constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 503, Throwable $previous = null)
    {
        if ($message === '') {
            $message = __('planning_exists');
        }

        parent::__construct($message, $code, $previous);
    }

}
