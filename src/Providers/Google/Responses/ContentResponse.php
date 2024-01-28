<?php

declare(strict_types=1);

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google\Responses;


use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class ContentResponse
{
    /**
     * @var string[]
     */
    public array $texts;

    public function __construct(array $rawResponse)
    {
        foreach( ArrayUtility::get($rawResponse, 'parts', []) as $rawPart ) {
            $this->texts[] = ArrayUtility::get($rawPart, 'text', "");
        }
    }

    public function toString(): string
    {
        return implode("", $this->texts);
    }
}
