<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Ollama;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TakaakiMizuno\LLMJsonAdapter\Providers\Ollama\Requests\ChatRequest;
use TakaakiMizuno\LLMJsonAdapter\Providers\Ollama\Responses\ChatResponse;

// https://ai.google.dev/tutorials/rest_quickstart

class APIClient
{
    protected string $url;

    protected string $model;

    public function __construct(
        string $url = 'http://localhost:11434',
        string $model = 'llama3',
    ) {
        $this->url = $url;
        $this->model = $model;
    }

    /**
     * @throws GuzzleException
     */
    public function generateChat(array $messages): ChatResponse
    {
        $client = new Client([
            'base_uri' => $this->url,
            'timeout' => 60,
        ]);
        $path = '/api/chat';

        $request = new ChatRequest($this->model, $messages);
        $response = $client->request(
            'POST',
            $path,
            [
                'json' => $request->toArray(),
            ]
        );
        $rawData = json_decode($response->getBody()->getContents(), true);
        return new ChatResponse($rawData);
    }

}
