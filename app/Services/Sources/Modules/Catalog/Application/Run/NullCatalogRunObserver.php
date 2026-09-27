<?php

namespace App\Services\Sources\Modules\Catalog\Application\Run;

use App\Services\Sources\Modules\Catalog\Contracts\CatalogRunObserver;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Domain\Results\CategoryRunResult;

class NullCatalogRunObserver implements CatalogRunObserver
{
    public function sourceStarted(CatalogSource $source, int $position, int $total): void {}

    public function categoryFinished(CategoryRunResult $result): void {}
}
