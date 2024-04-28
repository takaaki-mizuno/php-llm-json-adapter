<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers;

use TakaakiMizuno\LLMJsonAdapter\Exceptions\Exception;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Utilities\ArrayUtility;

class Provider
{
    protected array $attributes;

    protected array $requiredAttributes = [];

    public const LANGUAGES = [
        'en' => 'English',
        'ja' => 'Japanese',
    ];

    /**
     * @throws Exception
     */
    public function __construct(
        array $attributes
    ) {
        $this->attributes = $attributes;
        $this->validateAttributes();
    }

    /**
     * @throws Exception
     */
    protected function validateAttributes(): bool
    {
        foreach ($this->requiredAttributes as $requiredAttribute => $defaultValue) {

            if (!array_key_exists($requiredAttribute, $this->attributes) || empty($this->attributes[$requiredAttribute])) {
                if ($defaultValue === null) {
                    throw new Exception("Required attribute '{$requiredAttribute}' not found.");
                }
                $this->attributes[$requiredAttribute] = $defaultValue;
            }
        }
        return true;
    }

    protected function getAttribute(string $key, mixed $defaultValue): mixed
    {
        if (!array_key_exists($key, $this->attributes)) {
            return $defaultValue;
        }

        return $this->attributes[$key] ?? $defaultValue;
    }

    protected function getFullLanguageText(string $languageCode): string|null
    {
        return ArrayUtility::get(self::LANGUAGES, $languageCode, null);
    }

    protected function generateTextPrompt(
        string $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): string {
        $generatedPrompt = $prompt . "\n\n";
        $jsonFormat = json_encode($response->getSchema());

        if ($actAs !== null) {
            $generatedPrompt .= "Please answer as {$actAs}.\n\n";
        }

        if ($language !== null) {
            $fullLanguage = $this->getFullLanguageText($language);
            if($fullLanguage === null) {
                throw new RetryableException("Language {$language} is not supported.");
            }
            $generatedPrompt .= "Response should be in {$fullLanguage}.\n\n";
        }

        $generatedPrompt .= "And use Json format as the response format which defined as following: \n\n";
        $generatedPrompt .= "\n{$jsonFormat}\n\n";
        $generatedPrompt .= "and response json data should be wrapped by the markdown code block\n\n";

        return $generatedPrompt;
    }

    protected function generateChatPrompt(
        string $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        $fullLanguage = $this->getFullLanguageText($language);
        if($fullLanguage === null) {
            throw new RetryableException("Language {$language} is not supported.");
        }

        $jsonFormat = json_encode($response->getSchema());
        $messages = [
            [
                "role" => "user",
                "content" => $prompt,
            ],
            [
                "role" => "system",
                "content" => "You are {$actAs}. Response should be in {$fullLanguage}.",
            ],
            [
                "role" => "system",
                "content" => "use the following Json schema as the response format\n"
                    . "```{$jsonFormat}```\n\n"
                    . "- Response should be a single valid JSON data\n"
                    . "- Response should be wrapped by the markdown code block\n"
                    . "- Output only the json response\n"
                    . "- No need to output the format itself\n\n",
            ],
        ];

        return $messages;
    }

    protected function convertToLlamaPresentation(array $messages): string
    {
        $result = "<|begin_of_text|>";
        foreach ($messages as $message) {
            $role = $message['role'];
            $content = $message['content'];
            $result = $result . "<|start_header_id|>{$role}<|end_header_id|>\n{$content}<|eot_id|>";
        }
        return $result;
    }

    /**
     * @throws RetryableException
     */
    public function generate(
        string   $prompt,
        Response $response,
        string   $language = null,
        string   $actAs = null,
    ): array {
        throw new RetryableException('Provider not implemented.');
    }
}
