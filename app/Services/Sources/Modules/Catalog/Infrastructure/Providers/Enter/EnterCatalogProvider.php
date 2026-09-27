<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter;

use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\Jobs\SearchEnterCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;
use InvalidArgumentException;
use UnitEnum;

class EnterCatalogProvider implements CatalogProvider
{
    public function source(): CatalogSource
    {
        return CatalogSource::ENTER;
    }

    public function categories(): array
    {
        return EnterSearchParam::cases();
    }

    public function import(UnitEnum $category): void
    {
        $category = $this->category($category);
        (new ImportEnterCatalogCategory($category))->handle();
    }

    public function job(UnitEnum $category, ?string $runId = null): BaseCatalogSourceJob
    {
        return new SearchEnterCategoryJob($this->category($category), $runId);
    }

    private function category(UnitEnum $category): EnterSearchParam
    {
        return $category instanceof EnterSearchParam
            ? $category
            : throw new InvalidArgumentException('Enter provider received an unsupported category.');
    }
}
