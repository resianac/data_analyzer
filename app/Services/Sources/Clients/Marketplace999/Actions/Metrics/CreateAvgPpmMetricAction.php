<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions\Metrics;

use App\Services\Repository\MetricRepository;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values\GetAverageValueAction;
use App\Services\Sources\Data\MetricData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;

class CreateAvgPpmMetricAction
{
    /**
     * Calculate and creates new metrics about price per meter of flats
     *
     * @return array<MetricData>
     */
    public function handle(): array
    {
        $result = [];

        $metricRepository = new MetricRepository();
        $context = [
            "source" => SourceClientType::MARKETPLACE999,
            "filter_type" => EntityFilter::FLAT_DEFAULT
        ];

        $oneRoomAvg = (new GetAverageValueAction())->handle("pricePerMeter",
            "rooms","Apartament cu 1 cameră"
        );
        $twoRoomsAvg = (new GetAverageValueAction())->handle("pricePerMeter",
            "rooms","Apartament cu 2 camere"
        );

        $metrics = [
            [MetricKey::FLAT_AVG_PPM_1ROOM, $oneRoomAvg, "1 room"],
            [MetricKey::FLAT_AVG_PPM_2ROOMS, $twoRoomsAvg, "2 rooms"],
        ];

        foreach ($metrics as [$key, $value, $label]) {
            $result[] = $metricRepository->createMetric($context, $key, $value);
        }

        return MetricData::collect($result);
    }
}
