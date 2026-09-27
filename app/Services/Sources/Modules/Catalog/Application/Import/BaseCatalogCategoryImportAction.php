<?php

namespace App\Services\Sources\Modules\Catalog\Application\Import;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogEntitySink;
use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use App\Services\Sources\Shared\Contracts\ConfigInterface;
use App\Services\Sources\Shared\Data\PageResult;
use App\Services\Sources\Shared\Enums\EntityFilter;

abstract class BaseCatalogCategoryImportAction
{
    protected BaseClient $client;

    protected BaseSourceConfig|ConfigInterface $config;

    public function __construct(
        protected $searchParam,
        protected readonly EntityFilter $filter,
        private readonly ?CatalogEntitySink $sink = null,
    ) {
        $this->client = $this->createClient();
        $this->config = $this->client->getConfig();
    }

    abstract protected function createClient(): BaseClient;

    public function handle(): void
    {
        $page = 1;

        do {
            /** @var PageResult $result */
            $result = $this->client->search($this->filter, $this->searchParam, (string) $page);

            ($this->sink ?? app(CatalogEntitySink::class))->store(
                entities: $result->entities,
                category: $this->searchParam->name,
                metricFields: $this->config->get('metric_fields'),
            );

            if ($result->hasNextPage) {
                sleep(rand(
                    $this->config->get('sleep')['min'],
                    $this->config->get('sleep')['max'],
                ));
            }

            $page += $this->config->get('limit');
        } while ($result->hasNextPage);
    }
}
