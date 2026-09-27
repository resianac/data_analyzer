<?php

namespace App\Services\Sources\Modules\Catalog\Contracts;

use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Domain\Results\CategoryRunResult;

interface CatalogRunObserver
{
    public function sourceStarted(CatalogSource $source, int $position, int $total): void;

    public function categoryFinished(CategoryRunResult $result): void;
}
