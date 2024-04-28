<?php

namespace TakaakiMizuno\LLMJsonAdapter\Exceptions;

use Exception as BaseException;

class Exception extends BaseException
{
    protected Exception $originalException;

    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
