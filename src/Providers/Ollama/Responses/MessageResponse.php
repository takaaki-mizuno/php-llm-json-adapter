<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Ollama\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class MessageResponse
{
    public string $role;

    public string $content;

    public function __construct(array $rawResponse)
    {
        $this->role = ArrayUtility::get($rawResponse, 'role', "assistant");
        $this->content = ArrayUtility::get($rawResponse, 'content', "");
    }

    public function toString(): string
    {
        return $this->content;
    }
}
