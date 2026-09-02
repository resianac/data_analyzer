<?php

namespace App\Services\Sources\Data\SourceDataAttributes;

use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\SchemalessAttributes\SchemalessAttributes;

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
