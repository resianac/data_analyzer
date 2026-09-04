<?php

namespace App\Services\Sources\Clients\Cactus;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\MetricKey;

class CactusConfig extends BaseConfig
{
    public static string $baseUrl = "https://www.cactus.md/ru/catalogue/";

    protected array $fieldsToDuplicateCheck = [];

    protected function getDefaults(): array
    {
        return [
            "limit" => 1,
            'timeout' => 20,
            'cache_ttl' => 300,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'ru,en;q=0.9',
                'Cache-Control' => 'no-cache',
            ],
            "metric_fields" => [MetricKey::PRICE, MetricKey::DISCOUNT],
            "sleep" => [
                'min' => 0,
                'max' => 1,
            ],
        ];
    }
}
