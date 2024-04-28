<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Ollama;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Exception\GuzzleException;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider as BaseProvider;
use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

class Provider extends BaseProvider
{
    protected array $requiredAttributes = [
        'url' => "http://localhost:11434",
        'model' => 'llama3',
    ];

    public function __construct(
        array $attributes
    ) {
        parent::__construct($attributes);
    }

    public function getClient(): APIClient
    {
        return new APIClient(
            $this->getAttribute('url', 'http://localhost:11434'),
            $this->getAttribute('model', 'llama3')
        );
    }

    /**
     * @throws GuzzleException
     * @throws RetryableException
     */
    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        $client = $this->getClient();
        $messages = $this->generateChatPrompt($prompt, $response, $language, $actAs);
        $response = $client->generateChat($messages);

        $response = StringUtility::getJSONObjectFromMarkdownCodeBlock($response->message->content);
        if(is_array($response) && count($response) > 0) {
            return $response;
        }

        throw new RetryableException('Provider not implemented.');
    }
}
