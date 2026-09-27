<?php

namespace App\Services\Sources\Modules\Catalog\Contracts;

use Illuminate\Support\Collection;

interface CatalogEntitySink
{
    /** @param array<int, mixed> $metricFields */
    public function store(Collection $entities, string $category, array $metricFields): void;
}
