<?php

namespace App\Models;

use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    protected $guarded = [];

    protected $casts = [
        "key" => MetricKey::class,
        "value" => "array",
        "source" => SourceClientType::class,
        "filter_type" => EntityFilter::class,
    ];

    public function scopeContext(
        Builder $query,
        MetricKey $key,
        SourceClientType $source,
        EntityFilter $filterType
    ): Builder {
        return $query
            ->where('key', $key)
            ->where('source', $source)
            ->where('filter_type', $filterType);
    }

    public function scopeLatestValue(Builder $query): Builder
    {
        return $query->latest('created_at');
    }

    public static function getLatest(
        MetricKey $key,
        SourceClientType $source,
        EntityFilter $filterType
    ): mixed {
        return static::context($key, $source, $filterType)
            ->latestValue()
            ->first();
    }
}
