<?php

namespace App\Services\Sources\Clients\Bomba;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\MetricKey;

class BombaConfig extends BaseConfig
{
    public static string $baseUrl = "https://bomba.md/ru/category/";

    protected array $fieldsToDuplicateCheck = [];

    protected function getDefaults(): array
    {
        return [
            "limit" => 1,
            'timeout' => 20,
            'cache_ttl' => 300,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
                'Referer' => 'https://bomba.md/ru/',
                'Upgrade-Insecure-Requests' => '1',
                'Sec-Fetch-Dest' => 'document',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-User' => '?1',
            ],
            "metric_fields" => [MetricKey::PRICE, MetricKey::DISCOUNT],
            "sleep" => [
                'min' => 0,
                'max' => 1,
            ],
        ];
    }
}
