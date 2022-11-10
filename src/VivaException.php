<?php

namespace Sebdesign\VivaPayments;

use Exception;
use Throwable;

class VivaException extends Exception
{
    public function __construct(string $message, int $code, ?Throwable $previous = null)
    {
        parent::__construct("Error {$code}: {$message}", $code, $previous);
    }
}
