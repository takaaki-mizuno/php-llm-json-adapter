<?php

namespace TakaakiMizuno\LLMJsonAdapter\Utilities;

class StringUtility
{
    static public function camelCaseToSnakeCase($string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    static public function getJSONObjectFromMarkdownCodeBlock(string $markdown): array
    {
        preg_match('/```(json)?\r?\n(.*?)\r?\n```/s', $markdown, $matches);
        $json = $matches[2] ?? '';
        return json_decode($json, true);
    }
}
