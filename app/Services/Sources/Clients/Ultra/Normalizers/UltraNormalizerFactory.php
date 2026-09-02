<?php

namespace App\Services\Sources\Clients\Ultra\Normalizers;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Contracts\NormalizerInterface;
use RuntimeException;

class UltraNormalizerFactory
{
    public static array $map = [
         UltraSearchParam::TV->value => UltraTvMatchNormalizer::class,
         UltraSearchParam::FRIDGE->value => UltraFridgeMatchNormalizer::class,
    ];

    public static function make(UltraSearchParam $searchParam): NormalizerInterface
    {
        $normalizerClass = self::$map[$searchParam->value]
            ?? throw new RuntimeException(
                "No normalizer registered for Ultra search param [{$searchParam->value}]."
            );

        return new $normalizerClass();
    }
}
