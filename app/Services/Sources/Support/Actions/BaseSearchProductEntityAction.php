<?php

namespace App\Services\Sources\Support\Actions;

use App\Data\EntityMasterData;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Enums\EntityFilter;
use Illuminate\Pipeline\Pipeline;

abstract class BaseSearchProductEntityAction
{
    protected bool $hasNextPage = false;

    protected BaseClient $client;
    protected BaseConfig|ConfigInterface $config;

    public function __construct(
        protected $searchParam,
        protected readonly EntityFilter $filter,
    ) {
        $this->client = $this->createClient();
        $this->config = $this->client->getConfig();
    }

    abstract protected function createClient(): BaseClient;

    public function handle(): void
    {
        foreach ($this->paginate() as $page) {
            $result = $this->client->search($this->filter, $this->searchParam, $page);

            $this->hasNextPage = $this->client->hasNextPage();

            app(Pipeline::class)
                ->send($result)
                ->through([
                    StoreEntitiesPipe::make(
                        EntityMasterRepository::makeWithData(
                            EntityMasterData::from(['category' => strtolower($this->searchParam->name)])
                        ),
                        MetricTracker::make($this->config->get('metric_fields'))
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
