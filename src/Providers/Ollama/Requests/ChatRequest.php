<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Ollama\Requests;

use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

// https://github.com/ollama/ollama/blob/main/docs/api.md#generate-a-chat-completion

class ChatRequest
{
    protected string $model;

    protected array $messages;

    public function __construct(
        string $model,
        array $messages,
    ) {
        $this->messages = $messages;
        $this->model = $model;
    }

    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'stream' => false,
            'messages' => $this->messages,
        ];
    }
}
