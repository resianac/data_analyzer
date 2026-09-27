<?php

namespace App\Services\Sources\Modules\Catalog\Contracts;

use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;
use UnitEnum;

interface CatalogProvider
{
    public function source(): CatalogSource;

    /** @return array<int, UnitEnum> */
    public function categories(): array;

    public function import(UnitEnum $category): void;

    public function job(UnitEnum $category, ?string $runId = null): BaseCatalogSourceJob;
}
