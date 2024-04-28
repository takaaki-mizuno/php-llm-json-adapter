<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class ChatResponse
{
    public string $id;

    public string $object;

    public int $createdAt;

    public string $model;

    public array $choices;

    public int $promptTokenCount;

    public int $completionTokenCount;

    public int $totalTokenCount;

    public function __construct(array $rawResponse)
    {
        $this->id = ArrayUtility::get($rawResponse, 'id', '');
        $this->object = ArrayUtility::get($rawResponse, 'object', '');
        $this->createdAt = ArrayUtility::get($rawResponse, 'created_at', 0);
        $this->model = ArrayUtility::get($rawResponse, 'model', '');
        $rawChoices = ArrayUtility::get($rawResponse, 'choices', []);
        $this->choices = [];
        foreach ($rawChoices as $rawChoice) {
            $this->choices[] = new ChoiceResponse($rawChoice);
        }
        $usage = ArrayUtility::get($rawResponse, 'usage', []);
        $this->totalTokenCount = ArrayUtility::get($usage, 'total_tokens', 0);
        $this->promptTokenCount = ArrayUtility::get($usage, 'prompt_tokens', 0);
        $this->completionTokenCount = ArrayUtility::get($usage, 'completion_tokens', 0);
    }

    public function toArray(): array
    {
        $choices = [];
        foreach ($this->choices as $choice) {
            $choices[] = $choice->toArray();
        }

        $result = [
            'id' => $this->id,
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'model' => $this->model,
            'choices' => $choices,
            'prompt_token_count' => $this->promptTokenCount,
            'completion_token_count' => $this->completionTokenCount,
            'total_token_count' => $this->totalTokenCount,
        ];

        return $result;
    }

    public function getBestChoice(): ?ChoiceResponse
    {
        foreach ($this->choices as $choice) {
            return $choice;
        }

        return null;
    }
}
