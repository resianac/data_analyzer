<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\Jobs;

use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\ImportEnterCatalogCategory;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;

class SearchEnterCategoryJob extends BaseCatalogSourceJob
{
    public function __construct(
        public readonly EnterSearchParam $category,
        ?string $runId = null,
    ) {
        parent::__construct($runId);
    }

    protected function source(): string
    {
        return CatalogSource::ENTER->value;
    }

    protected function category(): string
    {
        return $this->category->name;
    }

    protected function runImport(): void
    {
        (new ImportEnterCatalogCategory($this->category))->handle();
    }
}
