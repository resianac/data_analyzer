<?php

namespace App\Services\Sources\Clients\Ultra\Actions;

use App\Services\Pipelines\EntityProcessing\FilterDuplicatesPipe;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Repository\EntityMasterRepository;
use App\Services\Repository\MetricTracker;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Enter\EnterClient;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\Marketplace999\Marketplace999Client;
use App\Services\Sources\Clients\RabotaMd\RabotaMdClient;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\UltraClient;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Data\EntityMasterData;
use App\Services\Sources\Drivers\GraphQLDriver;
use App\Services\Sources\Drivers\HtmlParserDriver;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use Illuminate\Pipeline\Pipeline;

class SearchUltraEntitiesAction
{
    private bool $hasNextPage = false;
    private BaseClient $client;
    private BaseConfig|ConfigInterface $config;

    public function __construct(
        private readonly UltraSearchParam $searchParam,
        private readonly EntityFilter     $filter = EntityFilter::ULTRA_ENTITY,
    ) {
        $this->client = new UltraClient(HtmlParserDriver::make());
        $this->config = $this->client->getConfig();
    }

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
