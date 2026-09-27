<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Persistence;

use App\Data\EntityMasterData;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogEntitySink;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

class EloquentCatalogEntitySink implements CatalogEntitySink
{
    public function __construct(private readonly Pipeline $pipeline) {}

    public function store(Collection $entities, string $category, array $metricFields): void
    {
        $this->pipeline
            ->send($entities)
            ->through([
                StoreEntitiesPipe::make(
                    EntityMasterRepository::makeWithData(
                        EntityMasterData::from(['category' => strtolower($category)])
                    ),
                    MetricTracker::make($metricFields),
                ),
            ])
            ->thenReturn();
    }
}
