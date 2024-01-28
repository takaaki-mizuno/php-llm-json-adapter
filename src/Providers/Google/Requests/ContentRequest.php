<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google\Requests;

use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

// https://ai.google.dev/tutorials/rest_quickstart

class ContentRequest
{
    protected string $prompt;

    public function __construct(
        string $prompt,
    ) {
        $this->prompt = $prompt;
    }

    public function toArray(): array
    {
        return [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $this->prompt,
                        ],
                    ],
                ],
            ],

        ];
    }
}
