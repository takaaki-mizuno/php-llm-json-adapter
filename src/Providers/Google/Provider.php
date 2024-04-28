<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google;

use GuzzleHttp\Exception\GuzzleException;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider as BaseProvider;
use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

class Provider extends BaseProvider
{
    protected array $requiredAttributes = [
        'api_key' => '',
        'model' => 'gemini-1.5-pro-latest',
    ];

    public function __construct(
        array $attributes
    ) {
        parent::__construct($attributes);
    }

    public function getClient(string $apiKey): APIClient
    {
        return new APIClient($apiKey);
    }

    /**
     * @throws RetryableException
     * @throws GuzzleException
     */
    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        $apiKey = $this->getAttribute('api_key', '');
        $client = $this->getClient($apiKey);

        $generateTextPrompt = $this->generateTextPrompt($prompt, $response, $language, $actAs);

        $result = $client->generateContent($generateTextPrompt);
        foreach($result->candidates as $candidate) {
            $response = StringUtility::getJSONObjectFromMarkdownCodeBlock($candidate->content->toString());
            if(is_array($response) && count($response) > 0) {
                return $response;
            }
        }
        throw new RetryableException('No json response candidate found.');
    }
}
