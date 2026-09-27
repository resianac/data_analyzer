<?php

namespace App\Services\Sources\Clients\Ultra\Jobs;

use App\Services\Sources\Clients\Ultra\Actions\SearchUltraEntitiesAction;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Support\Jobs\BaseCatalogSourceJob;

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

    protected function runSearch(): void
    {
        (new SearchUltraEntitiesAction($this->category))->handle();
    }
}
