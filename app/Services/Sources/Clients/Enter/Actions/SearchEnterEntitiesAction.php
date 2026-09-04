<?php

namespace App\Services\Sources\Clients\Enter\Actions;

use App\Data\EntityMasterData;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Enter\EnterClient;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;
use Illuminate\Pipeline\Pipeline;

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
