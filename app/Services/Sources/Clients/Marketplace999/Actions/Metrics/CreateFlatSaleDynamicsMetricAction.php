<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions\Metrics;

use App\Services\Repository\MetricRepository;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Generators\GetFlatSaleDynamicsGeneratorAction;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values\GetAverageValueAction;
use App\Services\Sources\Data\MetricData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreateFlatSaleDynamicsMetricAction
{
    public function __construct(
        protected MetricRepository $metricRepository = new MetricRepository()
    ) {}

    /**
     * Calculate and creates new metrics about price per meter of flats
     *
     * @return Collection<MetricData>
     */
    public function handle(Carbon $from = null, Carbon $to = null): Collection
    {
        $fromDate = $from ?? now()->subWeek()->startOfWeek();
        $toDate = $to ?? now()->subWeek()->endOfWeek();

        $metrics = (new GetFlatSaleDynamicsGeneratorAction())->handle($fromDate, $toDate);

        $result = collect();

        $context = [
            "source" => SourceClientType::MARKETPLACE999,
            "filter_type" => EntityFilter::FLAT_DEFAULT
        ];

        foreach ($metrics as $metric) {
            $key   = $metric['key'];
            $value = $metric['value'];

            $result->push(
                $this->metricRepository->createMetric($context, $key, $value)
            );
        }

        return MetricData::collect($result);
    }
}
