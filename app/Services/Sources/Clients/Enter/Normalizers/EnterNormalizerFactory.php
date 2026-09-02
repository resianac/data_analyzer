<?php

namespace App\Services\Sources\Clients\Enter\Normalizers;

use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Contracts\NormalizerInterface;
use RuntimeException;

class EnterNormalizerFactory
{
    public static array $map = [
        EnterSearchParam::TV->value => EnterTvMatchNormalizer::class,
        EnterSearchParam::FRIDGE->value => EnterFridgeMatchNormalizer::class,
    ];

    public static function make(EnterSearchParam $searchParam): NormalizerInterface
    {
        $normalizerClass = self::$map[$searchParam->value]
            ?? throw new RuntimeException(
                "No normalizer registered for Enter search param [{$searchParam->value}]."
            );

        return new $normalizerClass();
    }
}
