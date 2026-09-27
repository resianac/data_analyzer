<?php

namespace App\Services\Sources\Shared\Contracts;

use Spatie\LaravelData\Data;

interface NormalizerInterface
{
    public function normalize(Data $data): ?string;
}
