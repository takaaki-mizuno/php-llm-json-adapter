<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google\Responses;


use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class CandidateResponse
{
    public ContentResponse $content;

    public string $finishReason;
    /**
     * @var RatingResponse[]
     */
    public array $safetyRatings;

    public function __construct(array $rawResponse)
    {
        $rawContent = ArrayUtility::get($rawResponse, 'content', []);
        $this->content = new ContentResponse($rawContent);
        $this->finishReason = ArrayUtility::get($rawResponse, 'finishReason', "");
        $this->safetyRatings = [];
        foreach( ArrayUtility::get($rawResponse, 'safetyRatings', []) as $rawSafetyRating ) {
            $this->safetyRatings[] = new RatingResponse($rawSafetyRating);
        }
    }
}
