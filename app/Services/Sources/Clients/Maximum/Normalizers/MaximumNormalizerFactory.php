<?php

namespace App\Services\Sources\Clients\Maximum\Normalizers;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Contracts\NormalizerInterface;
use RuntimeException;

class MaximumNormalizerFactory
{
    public static array $map = [
         MaximumSearchParam::TV->value => MaximumTvMatchNormalizer::class,
    ];

    public static function make(MaximumSearchParam $searchParam): NormalizerInterface
    {
        $normalizerClass = self::$map[$searchParam->value]
            ?? throw new RuntimeException(
                "No normalizer registered for Maximum search param [{$searchParam->value}]."
            );

        return new $normalizerClass();
    }
}
