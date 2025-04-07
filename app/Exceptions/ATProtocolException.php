<?php

namespace App\Exceptions;

use Exception;

class ATProtocolException extends Exception
{
    /**
     * Create a new AT Protocol exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report or log the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        \Log::error('AT Protocol Error', [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
        ]);

        return false;
    }
} 