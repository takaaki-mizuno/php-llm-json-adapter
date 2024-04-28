<?php

namespace TakaakiMizuno\LLMJsonAdapter\Utilities;

class StringUtility
{
    public static function camelCaseToSnakeCase($string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function getJSONObjectFromMarkdownCodeBlock(string $markdown): ?array
    {
        preg_match('/```(json)?\r?\n(.*?)\r?\n```/s', $markdown, $matches);
        if($matches === null || count($matches) < 3) {
            return null;
        }
        $json = $matches[2] ?? '';
        return json_decode($json, true);
    }
}
