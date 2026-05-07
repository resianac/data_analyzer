<?php

namespace App\Services\Sources\Configs;

use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Enums\EntityFilter;

abstract class BaseConfig implements ConfigInterface
{
    public static string $baseUrl;

    protected array $config = [];

    /**
     * Fields to check for duplicates per entity type
     * @var array<string, array<string>>
     */
    protected array $fieldsToDuplicateCheck = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            [...$this->getDefaults(), "base_url" => static::$baseUrl],
            $config,
        );
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function all(): array
    {
        return $this->config;
    }

    public function getFieldsToCheck(EntityFilter $filter): array
    {
        return $this->fieldsToDuplicateCheck[$filter->value] ?? [];
    }

    protected function getDefaults(): array
    {
        return [
            "limit" => 50,
            'timeout' => 30,
            'cache_ttl' => 300,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'retry_attempts' => 3,
            'rate_limit' => 10,
        ];
    }
}
