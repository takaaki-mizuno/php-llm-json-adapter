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

    const LANGUAGES = [
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
