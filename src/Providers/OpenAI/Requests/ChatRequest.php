<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests;

// https://platform.openai.com/docs/api-reference/chat/create

use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

class ChatRequest
{
    public string $model;

    public array $messages;

    public ?array $functions;

    public ?string $toolChoice;

    public ?int $frequencyPenalty;

    public ?array $logitBias;

    public ?int $maxTokens;

    public ?float $presencePenalty;

    public ?float $stop;

    public ?float $temperature;

    public ?string $topP;

    public function __construct(
        string $model,
        ?array $messages = [],
        array $functions = null,
        string $toolChoice = null,
        int $frequencyPenalty = null,
        array $logitBias = null,
        int $maxTokens = null,
        float $presencePenalty = null,
        float $stop = null,
        float $temperature = null,
        string $topP = null
    ) {
        $this->model = $model;
        $this->messages = $messages;
        $this->functions = $functions;
        $this->toolChoice = $toolChoice;
        $this->frequencyPenalty = $frequencyPenalty;
        $this->logitBias = $logitBias;
        $this->maxTokens = $maxTokens;
        $this->presencePenalty = $presencePenalty;
        $this->stop = $stop;
        $this->temperature = $temperature;
        $this->topP = $topP;
    }

    public function toArray(): array
    {
        $messages = [];
        foreach ($this->messages as $message) {
            $messages[] = $message->toArray();
        }

        $result = [
            'model' => $this->model,
            'messages' => $messages,
        ];

        if ($this->functions !== null) {
            $functions = [];
            foreach ($this->functions as $function) {
                $functions[] = $function->toArray();
            }
            $result['tools'] = $functions;
            if ($this->toolChoice !== null) {
                $result['tool_choice'] = $this->toolChoice;
            } elseif (count($functions) == 1) {
                $result['tool_choice'] = [
                    'name' => $functions[0]->name,
                ];
            } else {
                $result['tool_choice'] = 'auto';
            }
        }
        foreach ([
                     'frequencyPenalty',
                     'logitBias',
                     'maxTokens',
                     'presencePenalty',
                     'stop',
                     'temperature',
                     'topP',
                 ] as $key) {
            if ($this->$key !== null) {
                $result[StringUtility::camelCaseToSnakeCase($key)] = $this->$key;
            }
        }

        return $result;
    }
}
