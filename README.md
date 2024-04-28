# LLM JSON Adapter

## What is it ?

When using LLMs from the system, you often expect to get output results in JSON: OpenAPI's GPT API has a mechanism called Function Calling, which can return JSON, but Google's Gemini does not seem to have that functionality.

Therefore, I have created a wrapper library to switch LLMs and get results in JSON. What this library can do is as follows.

- Allows you to define the results you want to get in JSON Schema
- Switch between LLMs (currently supports OpenAI's GPT and Google's Gemini).
- Retry a specified number of times if the JSON retrieval fails

## Installation

```sh
composer require takaaki-mizuno/llm-json-adapter
```

## How to use

Use the following code to get the results in JSON.

### OpenAI

```php
$instance = new LLMJsonAdapter(
    providerName: "openai",
    attributes: [
        "api_key" => "[API-KEY]",
        "model" => "gpt-3.5-turbo",
    ],
    maximumRetryCount: 3,
    model: "gpt-3.5-turbo",
    defaultLanguage: "en"
);

$response = new \TakaakiMizuno\LLMJsonAdapter\Models\Response(
    name: "response data name",
    description: "response data description",
    schema: [JSON SCHEMA]
);
```

### Google Gemini

```php
$instance = new LLMJsonAdapter(
    providerName: "google",
    attributes: [
        "api_key" => "[API-KEY]",
        "model" => "gemini-1.5-pro-latest",
    ],
    maximumRetryCount: 3,
    defaultLanguage: "en"
);

$response = new \TakaakiMizuno\LLMJsonAdapter\Models\Response(
    name: "response data name",
    description: "response data description",
    schema: [JSON SCHEMA]
);
```

### BedRock

```php
$instance = new LLMJsonAdapter(
    providerName: "bedrock",
    attributes: [
        'accessKeyId' => '[ACCESS-KEY]',
        'secretAccessKey' => '[SECRET-KEY]',
        'model' => 'anthropic.claude-3-haiku-20240307-v1:0',
    ],
    maximumRetryCount: 3,
    defaultLanguage: "en"
);

$response = new \TakaakiMizuno\LLMJsonAdapter\Models\Response(
    name: "response data name",
    description: "response data description",
    schema: [JSON SCHEMA]
);
```

### Ollama

```php
$instance = new LLMJsonAdapter(
    providerName: "ollama",
    attributes: [
        'url' => "http://localhost:11434",
        'model' => 'llama3',
    ],
    maximumRetryCount: 3,
    defaultLanguage: "en"
);

$response = new \TakaakiMizuno\LLMJsonAdapter\Models\Response(
    name: "response data name",
    description: "response data description",
    schema: [JSON SCHEMA]
);
```
