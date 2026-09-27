<?php

namespace App\Services\Sources\Clients\RabotaMd;

use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use App\Services\Sources\Shared\Enums\EntityFilter;

class RabotaMdConfig extends BaseSourceConfig
{
    public static string $baseUrl = 'https://www.rabota.md/ru/';

    protected array $fieldsToDuplicateCheck = [
        EntityFilter::JOB->value => ['title', 'company', 'city', 'salary'],
    ];

    protected function getDefaults(): array
    {
        return [
            'limit' => 1,
            'timeout' => 20,
            'cache_ttl' => 300,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'ru,en;q=0.9',
                'Cache-Control' => 'no-cache',
            ],
        ];
    }
}
