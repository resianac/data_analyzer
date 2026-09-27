<?php

namespace App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Clients\Maximum\MaximumClient;
use App\Services\Sources\Modules\Catalog\Application\Import\BaseCatalogCategoryImportAction;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Transport\HtmlTransport;

class ImportMaximumCatalogCategory extends BaseCatalogCategoryImportAction
{
    public function __construct(MaximumSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::MAXIMUM_ENTITY);
    }

    protected function createClient(): MaximumClient
    {
        return new MaximumClient(HtmlTransport::make());
    }
}
