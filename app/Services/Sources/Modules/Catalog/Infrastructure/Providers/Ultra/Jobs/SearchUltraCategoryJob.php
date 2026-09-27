<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\Jobs;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\ImportUltraCatalogCategory;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;

class SearchUltraCategoryJob extends BaseCatalogSourceJob
{
    public function __construct(
        public readonly UltraSearchParam $category,
        ?string $runId = null,
    ) {
        parent::__construct($runId);
    }

    protected function source(): string
    {
        return CatalogSource::ULTRA->value;
    }

    protected function category(): string
    {
        return $this->category->name;
    }

    protected function runImport(): void
    {
        (new ImportUltraCatalogCategory($this->category))->handle();
    }
}
