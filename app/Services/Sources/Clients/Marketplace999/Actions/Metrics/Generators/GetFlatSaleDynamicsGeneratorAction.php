<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Generators;

use App\Models\Entity;
use App\Services\Repository\MetricRepository;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values\GetAverageValueAction;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values\GetMostFrequentValueAction;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GetFlatSaleDynamicsGeneratorAction
{
    protected Builder $baseQuery;

    public function __construct() {}

    /**
     * @param Carbon|null $from (included)
     * @param Carbon|null $to (included)
     * @return array
     */
    public function handle(?Carbon $from = null, Carbon $to = null): array
    {
        $this->baseQuery = Entity::query()
            ->where('source', SourceClientType::MARKETPLACE999)
            ->where('filter_type', EntityFilter::FLAT_DEFAULT);

        if ($from || $to) {
            if ($from) {
                $this->baseQuery->where('updated_at', '>=', $from);
            }

            $this->baseQuery->where('updated_at', '<=', $to ?? now()->startOfHour());
        }

        return $this->calculateAllMetrics($this->baseQuery);
    }

    /**
     * All calculations
     *
     * @return array<int, array{key: string, value: float|int|string|null}>
     */
    private function calculateAllMetrics(Builder $query): array
    {
        $totalCount = $query->count();

        if ($totalCount === 0) {
            return $this->zeroMetrics();
        }

        $metrics[] = [
            'key'   => MetricKey::FLAT_TOTAL_SOLD,
            'value' => $totalCount,
        ];

        $metrics[] = [
            'key'   => MetricKey::FLAT_AVG_SOLD_PRICE,
            'value' => (new GetAverageValueAction())->handle("price", context:clone $this->baseQuery)
        ];

        $metrics[] = [
            'key'   => MetricKey::FLAT_AVG_SOLD_PPM,
            'value' => (new GetAverageValueAction())->handle("pricePerMeter", context:clone $this->baseQuery)
        ];

        $metrics[] = [
            'key'   => MetricKey::FLAT_TOP_SOLD_OWNER,
            'value' => (new GetMostFrequentValueAction())->handle("owner", context:clone $this->baseQuery),
        ];

        $metrics[] = [
            'key'   => MetricKey::FLAT_TOP_SOLD_TITLE,
            'value' => (new GetMostFrequentValueAction())->handle("title", context:clone $this->baseQuery),
        ];

        $avgDaysLive = $this->calculateAvgDaysLive(clone $query);
        if ($avgDaysLive !== null) {
            $metrics[] = [
                'key'   => MetricKey::FLAT_AVG_DAYS_STAYING,
                'value' => round($avgDaysLive, 1),
            ];
        }

        return $metrics;
    }

    private function calculateAvgDaysLive(Builder $query): ?float
    {
        if (app()->isProduction()) {
            return $query
                ->whereNotNull('data->posted')
                ->whereRaw("JSON_TYPE(data->'$.posted') != 'NULL'")
                ->selectRaw("AVG(DATEDIFF(updated_at, data->>'$.posted')) as avg_days")
                ->value('avg_days');
        }

        return $query
            ->whereNotNull('data->posted')
            ->selectRaw("AVG(JULIANDAY(updated_at) - JULIANDAY(data->>'posted')) as avg_days")
            ->value('avg_days');
    }

    private function zeroMetrics(): array
    {
        return [
            ['key' => MetricKey::FLAT_TOTAL_SOLD, 'value' => 0],
            ['key' => MetricKey::FLAT_AVG_SOLD_PPM, 'value' => null],
            ['key' => MetricKey::FLAT_AVG_SOLD_PRICE, 'value' => null],
            ['key' => MetricKey::FLAT_TOP_SOLD_OWNER, 'value' => null],
            ['key' => MetricKey::FLAT_TOP_SOLD_TITLE, 'value' => null],
            ['key' => MetricKey::FLAT_AVG_DAYS_STAYING, 'value' => null],
        ];
    }
}
