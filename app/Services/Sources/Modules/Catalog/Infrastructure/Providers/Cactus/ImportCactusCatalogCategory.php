<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus;

use App\Services\Sources\Clients\Cactus\CactusClient;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Modules\Catalog\Application\Import\BaseCatalogCategoryImportAction;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Transport\HtmlTransport;

class ImportCactusCatalogCategory extends BaseCatalogCategoryImportAction
{
    public function __construct(CactusSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::CACTUS_ENTITY);
    }

    protected function createClient(): CactusClient
    {
        return new CactusClient(HtmlTransport::make());
    }
}
