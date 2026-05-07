<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions;

use App\Services\Pipelines\EntityProcessing\FilterDuplicatesPipe;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Marketplace999\Marketplace999Client;
use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Drivers\GraphQLDriver;
use App\Services\Sources\Enums\EntityFilter;
use Illuminate\Pipeline\Pipeline;

class SearchFlatsAction
{
    private int|null $count;
    private BaseClient $client;
    private BaseConfig|ConfigInterface $config;

    public function __construct()
    {
        $this->client = new Marketplace999Client(GraphQLDriver::make());
        $this->config = $this->client->getConfig();
    }

    public function handle(): void
    {
        foreach ($this->paginate() as $skip) {
            $result = $this->client->flatsSearch(
                EntityFilter::FLAT_DEFAULT,
                $skip
            );

            if (!isset($this->count)) {
                $this->count = $this->client->getEntitiesCount();
            }

            app(Pipeline::class)
                ->send($result)
                ->through([
                    FilterDuplicatesPipe::make(
                        $this->config->getFieldsToCheck(EntityFilter::FLAT_DEFAULT)
                    ),
                    StoreEntitiesPipe::class,
                ])
                ->thenReturn();

            sleep(rand(10, 30));
        }
    }

    private function paginate(): iterable
    {
        $skip = 0;
        $isFirstIteration = true;

        while ($isFirstIteration || ($this->count === null || $skip < $this->count)) {
            $isFirstIteration = false;

            yield $skip;
            $skip += $this->config->get('limit');
        }
    }
}
