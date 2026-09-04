<?php

namespace App\Services\Sources\Clients\Maximum\Actions;

use App\Data\EntityMasterData;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Clients\Maximum\MaximumClient;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\UltraClient;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Support\Actions\BaseSearchProductEntityAction;
use Illuminate\Pipeline\Pipeline;

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
