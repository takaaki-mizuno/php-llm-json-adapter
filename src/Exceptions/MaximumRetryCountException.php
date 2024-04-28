<?php

namespace TakaakiMizuno\LLMJsonAdapter\Exceptions;

use Exception;

class MaximumRetryCountException extends Exception
{
    protected array $exceptions = [];

    public function __construct(string $message, array $exceptions = [])
    {
        foreach ($exceptions as $exception) {
            $message .= PHP_EOL . $exception->getMessage();
        }
        parent::__construct($message);
        $this->exceptions = $exceptions;
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}
