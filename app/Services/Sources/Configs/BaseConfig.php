<?php

namespace App\Services\Sources\Configs;

use App\Services\Sources\Contracts\ConfigInterface;

abstract class BaseConfig implements ConfigInterface
{
    public string $baseApiUrl;
    public static string $baseUrl;
    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            [...$this->getDefaults(), "base_url" => $this->baseApiUrl],
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
