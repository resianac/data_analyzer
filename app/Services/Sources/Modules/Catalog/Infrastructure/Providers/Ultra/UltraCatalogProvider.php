<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\Jobs\SearchUltraCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Queue\BaseCatalogSourceJob;
use InvalidArgumentException;
use UnitEnum;

class UltraCatalogProvider implements CatalogProvider
{
    public function source(): CatalogSource
    {
        return CatalogSource::ULTRA;
    }

    public function categories(): array
    {
        return UltraSearchParam::cases();
    }

    public function import(UnitEnum $category): void
    {
        $category = $this->category($category);
        (new ImportUltraCatalogCategory($category))->handle();
    }

    public function job(UnitEnum $category, ?string $runId = null): BaseCatalogSourceJob
    {
        return new SearchUltraCategoryJob($this->category($category), $runId);
    }

    private function category(UnitEnum $category): UltraSearchParam
    {
        return $category instanceof UltraSearchParam
            ? $category
            : throw new InvalidArgumentException('Ultra provider received an unsupported category.');
    }
}
