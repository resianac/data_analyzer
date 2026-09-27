<?php

namespace App\Services\Sources\Shared\Data;

use Spatie\LaravelData\Data;

class ProductAttributes extends Data
{
    public function __construct(
        public float $price,
        public ?float $old_price,
        public ?float $discount,
        public string $currency,
        public ?string $brand,
        public bool $is_out_of_stock,
        public ?string $url,
        public ?string $image,
        public array $raw = [],
    ) {}
}
