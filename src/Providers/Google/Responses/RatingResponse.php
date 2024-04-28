<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class RatingResponse
{
    public string $category;

    public string $probability;

    public function __construct(array $rawResponse)
    {
        $this->category = ArrayUtility::get($rawResponse, 'category', "");
        $this->probability = ArrayUtility::get($rawResponse, 'probability', "");
    }
}
