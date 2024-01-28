<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google;

use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider as BaseProvider;
use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

class Provider extends BaseProvider
{
    protected array $requiredAttributes = [
        'api_key' => '',
        'model' => 'gemini-pro',
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

    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        $apiKey = $this->getAttribute('api_key', '');
        $client = $this->getClient($apiKey);

        $generatedPrompt = $prompt . "\n\n";

        if ($actAs !== null) {
            $generatedPrompt .= "Please answer as {$actAs}.\n\n";
        }

        if ($language !== null) {
            $fullLanguage = $this->getFullLanguageText($language);
            if( $fullLanguage === null ){
                throw new RetryableException("Language {$language} is not supported.");
            }
            $generatedPrompt .= "Response should be in {$fullLanguage}.\n\n";
        }

        $jsonFormat = json_encode($response->getSchema());
        $generatedPrompt .= "And use Json format as the response format which defined as following: \n\n";
        $generatedPrompt .= "\n{$jsonFormat}\n\n";
        $generatedPrompt .= "and response json data should be wrapped by the markdown code block\n\n";

        $result = $client->generateContent($generatedPrompt);
        foreach( $result->candidates as $candidate ) {
            $response = StringUtility::getJSONObjectFromMarkdownCodeBlock($candidate->content->toString());
            if( is_array($response) && count($response) > 0 ) {
                return $response;
            }
        }
        throw new RetryableException('No json response candidate found.');
    }
}
