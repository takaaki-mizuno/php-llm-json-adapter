<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests;

class MessageRequest
{
    public string $role;

    public string $content;

    public function __construct(?string $role = 'user', ?string $content = '')
    {
        $this->role = $role;
        $this->content = $content;
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'content' => $this->content,
        ];
    }
}
