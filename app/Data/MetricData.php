<?php

namespace App\Data;

use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class MetricData extends Data
{
    public function __construct(
        public int|Optional         $id,
        public MetricKey            $key,
        public SourceClientType     $source,
        public EntityFilter         $filter_type,
        public mixed                $value,
    ) {}
}
