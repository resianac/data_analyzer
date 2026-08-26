<?php

namespace App\Services\Pipelines\EntityProcessing;

use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\EntityRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Data\EntityData;
use Closure;
use Illuminate\Support\Collection;
use Throwable;

readonly class StoreEntitiesPipe
{
    public function __construct(
        private EntityMasterRepository $masterRepository,
        private ?MetricTracker $metricTracker = null
    ) {}

    public static function make(
        EntityMasterRepository $masterRepository,
        ?MetricTracker $metricTracker = null
    ): self
    {
        return new self($masterRepository, $metricTracker);
    }

    /**
     * @param Collection<EntityData> $entities
     * @param Closure $next
     * @return mixed
     * @throws Throwable
     */
    public function handle(Collection $entities, Closure $next): mixed
    {
        (new EntityRepository($this->masterRepository, $this->metricTracker))->storeOrUpdateMany($entities);

        return $next($entities);
    }
}
