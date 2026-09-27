<?php

namespace App\Services\Sources\Configs;

use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\BaseConfig;

class Marketplace999Config extends BaseConfig
{
    public static string $baseUrl = "https://999.md/";

    public string $baseApiUrl = "https://999.md/graphql";

    protected array $fieldsToDuplicateCheck = [
        EntityFilter::FLAT_DEFAULT->value => ["owner", "price", "title", "pricePerMeter", "rooms"]
    ];
}
