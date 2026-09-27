<?php

namespace App\Services\Sources\Shared\Data\Casts;

use Spatie\LaravelData\Casts\Cast;

class DigitsCast implements Cast
{
    public function cast($property, mixed $value, array $properties, $context): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\D+/', '', (string) $value);
    }
}
