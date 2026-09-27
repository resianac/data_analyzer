<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\Jobs;

use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\ImportCactusCatalogCategory;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;

class SearchCactusCategoryJob extends BaseCatalogSourceJob
{
    public function __construct(
        public readonly CactusSearchParam $category,
        ?string $runId = null,
    ) {
        parent::__construct($runId);
    }

    protected function source(): string
    {
        return CatalogSource::CACTUS->value;
    }

    protected function category(): string
    {
        return $this->category->name;
    }

    protected function runImport(): void
    {
        (new ImportCactusCatalogCategory($this->category))->handle();
    }
}
