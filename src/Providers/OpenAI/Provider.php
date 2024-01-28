<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI;

use Orhanerday\OpenAi\OpenAi;
use TakaakiMizuno\LLMJsonAdapter\Exceptions\RetryableException;
use TakaakiMizuno\LLMJsonAdapter\Models\Response;
use TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests\ChatRequest;
use TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests\FunctionRequest;
use TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Requests\MessageRequest;
use TakaakiMizuno\LLMJsonAdapter\Providers\OpenAI\Responses\ChatResponse;
use TakaakiMizuno\LLMJsonAdapter\Providers\Provider as BaseProvider;

class Provider extends BaseProvider
{

    protected array $requiredAttributes = [
        'api_key' => '',
        'model' => 'gpt-3.5-turbo-1106',
        'temperature' => 0.67,
        'presence_penalty' => 0.0,
        'frequency_penalty' => 0.0,
    ];

    public function __construct(
        array $attributes
    )
    {
        parent::__construct($attributes);
    }

    public function getClient(string $apiKey): OpenAi
    {
        return new OpenAi($apiKey);
    }

    public function generate(
        string $prompt,
        Response $response,
        string $language = null,
        string $actAs = null,
    ): array
    {
        $apiKey = $this->getAttribute('api_key', '');
        $client = $this->getClient($apiKey);

        $messages = [];
        if( !empty($actAs) ){
            $messages[] = new MessageRequest('system', "you are a {$actAs}.");
        }

        $messages[] = new MessageRequest('user', $prompt);

        if( !empty($language)){
            $fullLanguage = $this->getFullLanguageText($language);
            if( $fullLanguage === null ){
                throw new RetryableException("Language {$language} is not supported.");
            }
            $messages[] = new MessageRequest('system', "language is {$fullLanguage}.");
        }

        $request = new ChatRequest(
            $this->getAttribute('model', 'gpt-3.5-turbo-1106'),
            $messages,
            [
                new FunctionRequest(
                    $response->getName(),
                    $response->getDescription(),
                    $response->getSchema()
                ),
            ],
            'auto'
        );

        $result = $client->chat($request->toArray());
        $chatResponse = new ChatResponse(json_decode($result, true));
        foreach( $chatResponse->choices as $choice ){
            if( count($choice->arguments) > 0 ){
                return $choice->arguments;
            }
        }

        throw new RetryableException('No stop choice found.');
    }
}
