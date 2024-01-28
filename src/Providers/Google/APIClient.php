<?php

namespace TakaakiMizuno\LLMJsonAdapter\Providers\Google;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use TakaakiMizuno\LLMJsonAdapter\Providers\Google\Requests\ContentRequest;
use TakaakiMizuno\LLMJsonAdapter\Providers\Google\Responses\ContentAPIResponse;

// https://ai.google.dev/tutorials/rest_quickstart

class APIClient
{
    protected string $apiKey;

    protected string $model;

    public function __construct(
        string $apiKey,
        string $model = 'gemini-pro',
    ) {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }

    /**
     * @throws GuzzleException
     */
    public function generateContent($prompt): ContentAPIResponse
    {
        $client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com',
            'timeout' => 60,
        ]);
        $path = '/v1beta/models/' . $this->model
            . ':generateContent?key=' . $this->apiKey;

        $request = new ContentRequest($prompt);
        $response = $client->request('POST', $path,
            [
                'json' => $request->toArray(),
            ]
        );
        $rawData = json_decode($response->getBody()->getContents(), true);
        return new ContentAPIResponse($rawData);
    }

}
