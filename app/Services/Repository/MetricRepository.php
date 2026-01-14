<?php

namespace App\Services\Repository;

use App\Models\Metric;
use App\Services\Sources\Data\MetricData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MetricRepository
{
    /**
     * @param array $context
     * @param MetricKey $key
     * @param float|string|array $value
     * @param Carbon|null $subDate
     * @return Metric
     */
    public function createMetric(
        array $context,
        MetricKey $key,
        mixed $value,
        ?Carbon $subDate = null
    ): Metric {
        $dateAgo = $subDate ?? now()->subDays(6);

        $metric = Metric::where($context)
            ->where('key', $key)
            ->where('created_at', '>=', $dateAgo)
            ->latest()
            ->first();

        if ($metric) {
            $metric->update([
                ...$context,
                'key' => $key,
                'value' => $value,
            ]);

            return $metric;
        }

        return Metric::create([
            ...$context,
            'key' => $key,
            'value' => $value,
        ]);
    }

    /**
     * @param SourceClientType $source
     * @param EntityFilter $filter
     * @param array<int, MetricKey> $keys
     * @return Collection
     */
    public function getPreviousMetrics(
        SourceClientType $source,
        EntityFilter $filter,
        array $keys,
    ): Collection {
        $metrics = collect();

        foreach ($keys as $key) {
            $metric = Metric::getLatest($key, $source, $filter);

            if ($metric) {
                $metrics->push($metric);
            }
        }

        return MetricData::collect($metrics);
    }
}
