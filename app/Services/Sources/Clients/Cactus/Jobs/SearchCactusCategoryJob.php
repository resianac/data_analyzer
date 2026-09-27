<?php

namespace App\Services\Sources\Clients\Cactus\Jobs;

use App\Services\Sources\Clients\Cactus\Actions\SearchCactusEntitiesAction;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Support\Jobs\BaseCatalogSourceJob;

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

    protected function runSearch(): void
    {
        (new SearchCactusEntitiesAction($this->category))->handle();
    }
}
