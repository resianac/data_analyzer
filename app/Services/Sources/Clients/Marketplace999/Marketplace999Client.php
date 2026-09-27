<?php

namespace App\Services\Sources\Clients\Marketplace999;

use App\Data\EntityData;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Marketplace999\Data\FlatData;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class Marketplace999Client extends BaseClient
{
    protected string $name = 'marketplace999';

    protected SourceClientType $type = SourceClientType::MARKETPLACE999;

    /**
     * @param  string  $schemaName  имя .graphql файла, например 'FlatsSearch'
     * @param  array  $variables  variables для GraphQL
     */
    public function execute(string $operationName, string $schemaName, array $variables): Collection
    {
        return $this->transport->executeQuery(
            "{$this->name}/{$operationName}/{$schemaName}",
            $variables,
        );
    }

    /**
     * @return Collection<EntityData>
     */
    public function flatsSearch(EntityFilter $filter, int $skip): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);
        $data = $this->execute(
            'searchAds',
            'FlatsSearch',
            $variableClass::base($this->config->get('limit'), $skip),
        );

        $ads = $data['data']['searchAds']['ads'] ?? [];
        $this->count = $data['data']['searchAds']['count'];

        return FlatData::collect($ads, Collection::class)
            ->map(
                fn (FlatData $flatData) => $flatData->toGeneral($filter)
            );
    }
}
