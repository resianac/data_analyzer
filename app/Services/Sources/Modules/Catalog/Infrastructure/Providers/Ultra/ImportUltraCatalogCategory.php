<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\UltraClient;
use App\Services\Sources\Modules\Catalog\Application\Import\BaseCatalogCategoryImportAction;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Transport\HtmlTransport;

class ImportUltraCatalogCategory extends BaseCatalogCategoryImportAction
{
    public function __construct(UltraSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::ULTRA_ENTITY);
    }

    protected function createClient(): UltraClient
    {
        return new UltraClient(HtmlTransport::make());
    }
}
