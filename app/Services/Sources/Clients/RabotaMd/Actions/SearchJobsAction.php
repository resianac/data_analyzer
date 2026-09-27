<?php

namespace App\Services\Sources\Clients\RabotaMd\Actions;

use App\Services\Pipelines\EntityProcessing\FilterDuplicatesPipe;
use App\Services\Pipelines\EntityProcessing\StoreEntitiesPipe;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\RabotaMd\RabotaMdClient;
use App\Services\Sources\Shared\Contracts\ConfigInterface;
use App\Services\Sources\Shared\Transport\HtmlTransport;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use Illuminate\Pipeline\Pipeline;

class SearchJobsAction
{
    private int|null $count = null;
    private BaseClient $client;
    private BaseSourceConfig|ConfigInterface $config;

    public function __construct()
    {
        $this->client = new RabotaMdClient(HtmlTransport::make());
        $this->config = $this->client->getConfig();
    }

    public function handle(string $operationName = "jobs-moldova-PHP"): void
    {
        foreach ($this->paginate() as $page) {
            $result = $this->client->jobSearch(
                EntityFilter::JOB,
                $operationName,
                $page
            );

            $this->count = $this->client->getEntitiesCount();

            app(Pipeline::class)
                ->send($result)
                ->through([
                    FilterDuplicatesPipe::make(
                        $this->config->getFieldsToCheck(EntityFilter::FLAT_DEFAULT)
                    ),
                    StoreEntitiesPipe::class,
                ])
                ->thenReturn();

            sleep(rand(2, 5));
        }
    }

    private function paginate(): iterable
    {
        $skip = 1;
        $isFirstIteration = true;

        while ($isFirstIteration || ($this->count === null || $this->count > 0)) {
            $isFirstIteration = false;

            yield $skip;
            $skip += $this->config->get('limit');
        }
    }
}
