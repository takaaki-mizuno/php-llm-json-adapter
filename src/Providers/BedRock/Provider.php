<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\BedRock;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider as BaseProvider;
use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;
use TakaakiMizuno\LLMJsonAdapter\Utilities\StringUtility;

class Provider extends BaseProvider
{
    protected array $requiredAttributes = [
        'accessKeyId' => null,
        'secretAccessKey' => null,
        'region' => 'us-east-1',
        'model' => 'anthropic.claude-3-haiku-20240307-v1:0',
        'maxTokens' => 1024,
    ];

    public function __construct(
        array $attributes
    ) {
        parent::__construct($attributes);
    }

    public function getClient(): BedrockRuntimeClient
    {
        return new BedrockRuntimeClient([
            'region' => $this->getAttribute('region', 'us-east-1'),
            'version' => '2023-09-30',
            'credentials' => [
                'key'    => $this->getAttribute('accessKeyId', null),
                'secret' => $this->getAttribute('secretAccessKey', null) ,
            ],
        ]);
    }

    protected function getModelProvider(string $model): string
    {
        return explode('.', $model)[0];
    }

    protected function generateBody(string $modelName, array $messages): array
    {
        $modelProvider = $this->getModelProvider($modelName);
        switch ($modelProvider) {
            case 'anthropic':
                $userMessages = [];
                $systemMessages = [];
                foreach ($messages as $message) {
                    if ($message['role'] === 'system') {
                        $systemMessages[] = $message['content'];
                    } elseif($message['role'] === 'user' || $message['role'] === 'assistant') {
                        $userMessages[] = $message;
                    }
                }

                return [
                    "anthropic_version" =>  "bedrock-2023-05-31",
                    "system" => implode("\n", $systemMessages),
                    "messages" => $userMessages,
                    "max_tokens" => $this->getAttribute('maxTokens', 1024),
                ];
            case 'meta':
                $prompt = $this->convertToLlamaPresentation($messages);
                return [
                    "prompt" => $prompt,
                    "temperature" => 0.5,
                    "top_p" => 0.9,
                    "max_gen_len" => $this->getAttribute('maxTokens', 1024),
                ];
            default:
                throw new RetryableException("Provider not implemented.");
        }
    }

    /**
     * @throws RetryableException
     */
    protected function extractContent(string $modelName, array $response): string
    {
        $modelProvider = $this->getModelProvider($modelName);
        switch ($modelProvider) {
            case 'anthropic':
                $contents = ArrayUtility::get($response, 'content', []);
                $response = [];
                foreach ($contents as $message) {
                    $type = ArrayUtility::get($message, 'type', "");
                    if($type === 'text') {
                        $response[] = ArrayUtility::get($message, 'text', "");
                    }
                }
                return implode("\n", $response);
            case 'meta':
                return ArrayUtility::get($response, 'generation', "");
            default:
                throw new RetryableException("Provider not implemented.");
        }
    }

    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        $client = $this->getClient();
        $modelName = $this->getAttribute('model', 'anthropic.claude-3-haiku-20240307-v1:0');

        $messages = $this->generateChatPrompt($prompt, $response, $language, $actAs);
        $body = $this->generateBody($modelName, $messages);
        $result = $client->invokeModel([
            'contentType' => 'application/json',
            'body' => json_encode($body),
            'modelId' => $modelName,
        ]);
        $responseBody = $result->get("body");
        $responseContent = json_decode($responseBody->getContents(), true);
        $content = $this->extractContent($modelName, $responseContent);
        $response = StringUtility::getJSONObjectFromMarkdownCodeBlock($content);
        if(is_array($response) && count($response) > 0) {
            return $response;
        }

        throw new RetryableException('Provider not implemented.');
    }
}
