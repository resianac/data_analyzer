<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\Jobs\SearchMaximumCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;
use InvalidArgumentException;
use UnitEnum;

class MaximumCatalogProvider implements CatalogProvider
{
    public function source(): CatalogSource
    {
        return CatalogSource::MAXIMUM;
    }

    public function categories(): array
    {
        return MaximumSearchParam::cases();
    }

    public function import(UnitEnum $category): void
    {
        $category = $this->category($category);
        (new ImportMaximumCatalogCategory($category))->handle();
    }

    public function job(UnitEnum $category, ?string $runId = null): BaseCatalogSourceJob
    {
        return new SearchMaximumCategoryJob($this->category($category), $runId);
    }

    private function category(UnitEnum $category): MaximumSearchParam
    {
        return $category instanceof MaximumSearchParam
            ? $category
            : throw new InvalidArgumentException('Maximum provider received an unsupported category.');
    }
}
