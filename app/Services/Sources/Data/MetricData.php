<?php

namespace App\Services\Sources\Data;

use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\SchemalessAttributes\SchemalessAttributes;

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
