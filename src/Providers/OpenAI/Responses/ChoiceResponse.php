<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class ChoiceResponse
{
    public string $finishReason;

    public int $index;

    public string $model;

    public string $choices;

    public ?string $content;

    public ?string $functionName;

    public ?array $arguments;

    public function __construct(array $rawResponse)
    {
        $this->finishReason = ArrayUtility::get($rawResponse, 'finish_reason', '');
        $this->index = ArrayUtility::get($rawResponse, 'index', 0);
        $message = ArrayUtility::get($rawResponse, 'message', []);
        $this->content = '';
        $this->functionName = '';
        $this->arguments = [];
        if (! empty($message)) {
            $this->content = ArrayUtility::get($message, 'content', '');
            $toolCalls = ArrayUtility::get($message, 'tool_calls', []);
            if (! empty($toolCalls)) {
                $toolCall = $toolCalls[0];
                $type = ArrayUtility::get($toolCall, 'type', '');
                if ($type === 'function') {
                    $function = ArrayUtility::get($toolCall, 'function');
                    if (! empty($function)) {
                        $this->functionName = ArrayUtility::get($function, 'name', '');
                        $this->arguments = json_decode(ArrayUtility::get($function, 'arguments', ''), true);
                    }
                }
            }
        }
    }

    public function toArray(): array
    {
        $result = [
            'finish_reason' => $this->finishReason,
            'index' => $this->index,
            'message' => [
                'content' => $this->content,
                'function_call' => [
                    'function_name' => $this->functionName,
                    'arguments' => $this->arguments,
                ],
            ],
        ];

        return $result;
    }
}
