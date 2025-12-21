<?php

namespace App\Services\Sources\Clients\Marketplace999\Cast;

use Carbon\Carbon;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class MarketplaceDateCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, $context): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $normalized = preg_replace_callback(
            '/\b([a-zа-яё]+)\./iu',
            fn ($m) => ucfirst($m[1]),
            $value
        );

        return Carbon::createFromFormat(
            'd M Y, H:i',
            $normalized,
            'Europe/Chisinau'
        );
    }
}
