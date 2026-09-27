<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus;

use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\Jobs\SearchCactusCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;
use InvalidArgumentException;
use UnitEnum;

class CactusCatalogProvider implements CatalogProvider
{
    public function source(): CatalogSource
    {
        return CatalogSource::CACTUS;
    }

    public function categories(): array
    {
        return CactusSearchParam::cases();
    }

    public function import(UnitEnum $category): void
    {
        $category = $this->category($category);
        (new ImportCactusCatalogCategory($category))->handle();
    }

    public function job(UnitEnum $category, ?string $runId = null): BaseCatalogSourceJob
    {
        return new SearchCactusCategoryJob($this->category($category), $runId);
    }

    private function category(UnitEnum $category): CactusSearchParam
    {
        return $category instanceof CactusSearchParam
            ? $category
            : throw new InvalidArgumentException('Cactus provider received an unsupported category.');
    }
}
