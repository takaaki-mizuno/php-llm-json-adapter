<?php

namespace TakaakiMizuno\LLMJsonAdapter;

use Swaggest\JsonSchema\InvalidValue;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\Exception;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\MaximumRetryCountException;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider;
use TakaakiMizuno\LLMJsonAdapter\Providers\Google\Provider as GoogleProvider;
use TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Provider as OpenAIProvider;
use Swaggest\JsonSchema\Schema;

class LLMJsonAdapter
{
    protected Provider $provider;

    protected string $defaultLanguage;

    protected int $maximumRetryCount;

    /**
     * @throws Exception
     */
    protected function getProvider(string $providerName, array $attributes): Provider
    {
        return match ($providerName) {
            'google' => new GoogleProvider($attributes),
            'openai' => new OpenAIProvider($attributes),
            default => throw new Exception('Provider not found.'),
        };
    }

    /**
     * @throws Exception
     */
    public function __construct(
        string $providerName,
        array  $attributes,
        int    $maximumRetryCount = 0,
        string $defaultLanguage = 'en',
    ) {
        $this->provider = $this->getProvider($providerName, $attributes);
        $this->maximumRetryCount = $maximumRetryCount;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * @throws \Swaggest\JsonSchema\Exception
     * @throws InvalidValue
     */
    protected function validateJsonSchema(array $data): bool
    {
        $filePath = 'file://' . realpath(dirname(__FILE__)
                . DIRECTORY_SEPARATOR . 'schemas'
                . DIRECTORY_SEPARATOR . "2020-12.schema.json");
        $rawJson = file_get_contents($filePath);
        $schema = Schema::import(json_decode($rawJson));
        try {
            $schema->in($data);
            return true;
        } catch (InvalidValue $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     * @throws MaximumRetryCountException
     */
    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ) {
        $language = $language ?? $this->defaultLanguage;

        if( $this->validateJsonSchema($response->getSchema()) ) {
            throw new Exception('Invalid JSON Schema.');
        }

        $retryCount = 0;
        /** @var []RetryableException $exceptions */
        $exceptions = [];

        while ($retryCount < $this->maximumRetryCount) {
            try {
                return $this->provider->generate($prompt, $response, $language, $actAs);
            } catch (RetryableException $e) {
                $exceptions[] = $e;
                $retryCount++;
            }
        }

        throw new MaximumRetryCountException('Maximum retry count exceeded.', $exceptions);
    }

}
