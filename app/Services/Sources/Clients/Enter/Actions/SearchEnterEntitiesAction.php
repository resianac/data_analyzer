<?php

namespace App\Services\Sources\Clients\Enter\Actions;

use App\Services\Sources\Clients\Enter\EnterClient;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;

class SearchEnterEntitiesAction extends BaseSearchProductEntityAction
{
    public function __construct(EnterSearchParam $searchParam)
    {
        parent::__construct($searchParam, EntityFilter::ENTER_ENTITY);
    }

    protected function createClient(): EnterClient
    {
        return new EnterClient(HtmlParserDriver::make());
    }
}
