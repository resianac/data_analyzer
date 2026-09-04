<?php

namespace App\Services\Sources\Clients\Bomba\Actions;

use App\Data\EntityMasterData;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Bomba\BombaClient;
use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use Illuminate\Pipeline\Pipeline;

class SearchBombaEntitiesAction
{
    private bool $hasNextPage = false;
    private BaseClient $client;
    private BaseConfig|ConfigInterface $config;

    public function __construct(
        private readonly BombaSearchParam $searchParam,
        private readonly EntityFilter     $filter = EntityFilter::BOMBA_ENTITY,
    ) {
        $this->client = new BombaClient(HtmlParserDriver::make());
        $this->config = $this->client->getConfig();
    }

    public function handle(): void
    {
        foreach ($this->paginate() as $page) {
            $result = $this->client->search($this->filter, $this->searchParam, $page);

            dd($result);

            $this->hasNextPage = $this->client->hasNextPage();

            app(Pipeline::class)
                ->send($result)
                ->through([
                    StoreEntitiesPipe::make(
                        EntityMasterRepository::makeWithData(
                            EntityMasterData::from(['category' => strtolower($this->searchParam->name)])
                        ),
                        MetricTracker::make($this->config->get('metric_fields')),
                    ),
                ])
                ->thenReturn();

            sleep(rand(
                $this->config->get('sleep')['min'],
                $this->config->get('sleep')['max']
            ));
        }
    }

    private function paginate(): iterable
    {
        $skip = 1;
        $isFirstIteration = true;

        while ($isFirstIteration || $this->hasNextPage) {
            $isFirstIteration = false;

            yield $skip;
            $skip += $this->config->get('limit');
        }
    }
}
