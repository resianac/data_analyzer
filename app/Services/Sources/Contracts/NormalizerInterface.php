<?php

namespace App\Services\Sources\Contracts;

use Spatie\LaravelData\Data;

interface NormalizerInterface
{
    public function normalize(Data $data): string|null;
}
