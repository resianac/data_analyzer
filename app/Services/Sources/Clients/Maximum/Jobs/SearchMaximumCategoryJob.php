<?php

namespace App\Services\Sources\Clients\Maximum\Jobs;

use App\Services\Sources\Clients\Maximum\Actions\SearchMaximumEntitiesAction;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Support\Jobs\BaseCatalogSourceJob;

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

    protected function runSearch(): void
    {
        (new SearchMaximumEntitiesAction($this->category))->handle();
    }
}
