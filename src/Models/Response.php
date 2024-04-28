<?php

namespace TakaakiMizuno\LLMJsonAdapter\Models;

class Response
{
    protected string $_name;

    protected string $_description;

    protected array $_schema;

    public function __construct(
        string $name,
        string $description,
        array $schema
    ) {
        $this->_name = $name;
        $this->_description = $description;
        $this->_schema = $schema;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getDescription(): string
    {
        return $this->_description;
    }

    public function getSchema(): array
    {
        return $this->_schema;
    }
}
