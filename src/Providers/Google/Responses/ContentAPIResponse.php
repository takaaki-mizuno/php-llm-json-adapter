<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google\Responses;

use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class ContentAPIResponse
{
    public array $candidates;

    public function __construct(array $rawResponse)
    {
        $rawCandidates = ArrayUtility::get($rawResponse, 'candidates', []);
        $this->candidates = [];
        foreach ($rawCandidates as $rawCandidate) {
            $this->candidates[] = new CandidateResponse($rawCandidate);
        }
    }
}
