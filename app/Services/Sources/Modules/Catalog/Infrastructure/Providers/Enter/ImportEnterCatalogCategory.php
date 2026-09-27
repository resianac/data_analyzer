<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter;

use App\Services\Sources\Clients\Enter\EnterClient;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Modules\Catalog\Application\Import\BaseCatalogCategoryImportAction;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Transport\HtmlTransport;

class ImportEnterCatalogCategory extends BaseCatalogCategoryImportAction
{
    public function __construct(EnterSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::ENTER_ENTITY);
    }

    protected function createClient(): EnterClient
    {
        return new EnterClient(HtmlTransport::make());
    }
}
