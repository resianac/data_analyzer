<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\Jobs;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\ImportMaximumCatalogCategory;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;

class SearchMaximumCategoryJob extends BaseCatalogSourceJob
{
    public function __construct(
        public readonly MaximumSearchParam $category,
        ?string $runId = null,
    ) {
        parent::__construct($runId);
    }

    protected function source(): string
    {
        return CatalogSource::MAXIMUM->value;
    }

    protected function category(): string
    {
        return $this->category->name;
    }

    protected function runImport(): void
    {
        (new ImportMaximumCatalogCategory($this->category))->handle();
    }
}
