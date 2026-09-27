<?php

namespace App\Services\Sources\Clients\Ultra\Actions;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\UltraClient;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;

class SearchUltraEntitiesAction extends BaseSearchProductEntityAction
{
    public function __construct(UltraSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::ULTRA_ENTITY);
    }

    protected function createClient(): UltraClient
    {
        return new UltraClient(HtmlParserDriver::make());
    }
}
