<?php

namespace App\Services\Sources\Clients\Bomba\Normalizers;

use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use App\Services\Sources\Contracts\NormalizerInterface;
use RuntimeException;

class BombaNormalizerFactory
{
    public static array $map = [
        // TODO: register normalizers for each search param
        // BombaSearchParam::EXAMPLE->value => ExampleBombaMatchNormalizer::class,
    ];

    public static function make(BombaSearchParam $searchParam): NormalizerInterface
    {
        $normalizerClass = self::$map[$searchParam->value]
            ?? throw new RuntimeException(
                "No normalizer registered for Bomba search param [{$searchParam->value}]."
            );

        return new $normalizerClass();
    }
}
