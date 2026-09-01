<?php

namespace App\Services\Repository;

use App\Data\MetricData;
use App\Models\Entity;
use App\Models\Metric;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

readonly class MetricTracker
{
    public function __construct(private array $fields = []) {}

    public static function make(array $fields = []): self
    {
        return new self($fields);
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    public function trackForByFields(
        Entity $entity,
    ): void {
        if (!$this->hasFields()) {
            throw new InvalidArgumentException('No metric fields configured for tracking.');
        }

        /* @var MetricKey $field */
        foreach ($this->fields as $field) {
            $this->trackFor($entity, $field);
        }
    }

    public function trackFor(
        Entity $entity,
        MetricKey $key,
    ): ?Metric {
        $lastMetric = $entity->metrics()
            ->where('key', $key)
            ->latest()
            ->first();

        $value = $entity->data->{$key->value} ?? 0;

        if ($lastMetric && $lastMetric->value == $value) {
            return null;
        }

        return $entity->metrics()->create([
            'key' => $key,
            'value' => $value,
            "source" => $entity->source,
            "filter_type" => $entity->filter_type
        ]);
    }

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
