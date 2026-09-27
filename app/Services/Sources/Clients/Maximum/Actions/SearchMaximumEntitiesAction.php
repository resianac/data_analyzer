<?php

namespace App\Services\Sources\Clients\Maximum\Actions;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Clients\Maximum\MaximumClient;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;

class SearchMaximumEntitiesAction extends BaseSearchProductEntityAction
{
    public function __construct(MaximumSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::MAXIMUM_ENTITY);
    }

    protected function createClient(): MaximumClient
    {
        return new MaximumClient(HtmlParserDriver::make());
    }
}
