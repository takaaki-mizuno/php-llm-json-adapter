<?php
namespace TakaakiMizuno\LLMJsonAdapter\Exceptions;

use Exception;

class RetryableException extends Exception
{
    protected Exception $originalException;

    public function __construct(string $message = '', Exception $originalException = null)
    {
        parent::__construct($message);
        $this->originalException = $originalException;
    }

    public function getOriginalException(): Exception
    {
        return $this->originalException;
    }
}
