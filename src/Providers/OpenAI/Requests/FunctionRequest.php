<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests;

class FunctionRequest
{
    public string $name;

    public string $description;

    public array $parameters;

    public function __construct(
        string $name = '',
        string $description = '',
        array $parameters = []
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
    }

    public function toArray(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name,
                'description' => $this->description,
                'parameters' => $this->parameters,
            ],
        ];
    }
}
