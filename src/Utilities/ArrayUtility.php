<?php

namespace TakaakiMizuno\LLMJsonAdapter\Utilities;

class ArrayUtility
{
    public static function get(array $array, string $key, mixed $defaultValue = null): mixed
    {
        if(!array_key_exists($key, $array)) {
            return $defaultValue;
        }

        return $array[$key] ?? $defaultValue;
    }
}
