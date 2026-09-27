<?php

namespace App\Services\Sources\Clients\Marketplace999;

use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use App\Services\Sources\Shared\Enums\EntityFilter;

class Marketplace999Config extends BaseSourceConfig
{
    public static string $baseUrl = 'https://999.md/';

    public string $baseApiUrl = 'https://999.md/graphql';

    protected array $fieldsToDuplicateCheck = [
        EntityFilter::FLAT_DEFAULT->value => ['owner', 'price', 'title', 'pricePerMeter', 'rooms'],
    ];
}
