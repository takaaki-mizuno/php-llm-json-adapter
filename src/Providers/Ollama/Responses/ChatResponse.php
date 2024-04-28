<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Ollama\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class ChatResponse
{
    public MessageResponse $message;

    public function __construct(array $rawResponse)
    {
        $rawMessage = ArrayUtility::get($rawResponse, 'message', []);
        $this->message = new MessageResponse($rawMessage);
    }

    public function toString(): string
    {
        return implode("", $this->message->content);
    }
}
