<?php
namespace TakaakiMizuno\LLMJsonAdapter\Tests;

use TakaakiMizuno\LLMJsonAdapter\Exceptions\Exception;
use TakaakiMizuno\LLMJsonAdapter\LLMJsonAdapter;

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetInstanceForOpenAI()
    {
        $instance = new LLMJsonAdapter(
            providerName: "openai",
            attributes: ["api_key" => "test"],
            maximumRetryCount: 3,
            defaultLanguage: "en"
        );
        $this->assertInstanceOf(LLMJsonAdapter::class, $instance);
    }

    public function testGetInstanceForGoogle()
    {
        $instance = new LLMJsonAdapter(
            providerName: "google",
            attributes: ["api_key" => "test"],
            maximumRetryCount: 3,
            defaultLanguage: "ja"
        );
        $this->assertInstanceOf(LLMJsonAdapter::class, $instance);
    }

}
