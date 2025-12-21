<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Marketplace999\Filters\FlatsVariables;
use App\Services\Sources\Clients\Marketplace999\Marketplace999Client;
use App\Services\Sources\Drivers\GraphQLDriver;
use App\Services\Sources\Repository\EntityRepository;

class SearchFlatsAction
{
    private int|null $count;
    private int $limit;
    private BaseClient $client;

    public function __construct()
    {
        $this->client = new Marketplace999Client(GraphQLDriver::make());
        $this->limit = $this->client->getConfig()->get("limit");
    }

    public function handle(): void
    {
        foreach ($this->paginate() as $skip) {
            $result = $this->client->flatsSearch(
                FlatsVariables::base($this->limit, $skip)
            );

            if (!isset($this->count)) {
                $this->count = $this->client->getEntitiesCount();
            }

            (new EntityRepository())->storeMany($result);

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
            $skip += $this->limit;
        }
    }
}
