<?php

namespace App\Services\Sources\Data\Casts;

use Spatie\LaravelData\Casts\Cast;

class NumberCast implements Cast
{
    public function cast($property, mixed $value, array $properties, $context): ?float
    {
        if ($value === null) {
            return null;
        }

        $cleaned = str_replace(["\u{A0}", ' ', ','], '', (string) $value);

        return (float) $cleaned;
    }
}
