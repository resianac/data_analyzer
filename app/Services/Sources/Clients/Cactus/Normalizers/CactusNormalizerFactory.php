<?php

namespace App\Services\Sources\Clients\Cactus\Normalizers;

use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Contracts\NormalizerInterface;
use RuntimeException;

class CactusNormalizerFactory
{
    public static array $map = [
         CactusSearchParam::TV->value => CactusTvMatchNormalizer::class,
    ];

    public static function make(CactusSearchParam $searchParam): NormalizerInterface
    {
        $normalizerClass = self::$map[$searchParam->value]
            ?? throw new RuntimeException(
                "No normalizer registered for Cactus search param [{$searchParam->value}]."
            );

        return new $normalizerClass();
    }
}
