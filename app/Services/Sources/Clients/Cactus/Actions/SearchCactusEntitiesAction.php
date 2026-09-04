<?php

namespace App\Services\Sources\Clients\Cactus\Actions;

use App\Services\Sources\Clients\Cactus\CactusClient;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;

class SearchCactusEntitiesAction extends BaseSearchProductEntityAction
{
    public function __construct(CactusSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::CACTUS_ENTITY);
    }

    protected function createClient(): CactusClient
    {
        return new CactusClient(HtmlParserDriver::make());
    }
}
