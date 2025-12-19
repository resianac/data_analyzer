<?php

namespace App\Services\Sources\Clients;

use App\Services\Sources\Enums\SourceClientType;

class Marketplace999Client extends BaseClient
{
    protected string $name = 'marketplace_999';
    protected SourceClientType $type = SourceClientType::MARKETPLACE;

    public function fetch(array $params = []): mixed
    {
        $query = $this->buildQuery($params);
        $variables = $this->buildVariables($params);

        return $this->driver->execute('POST', '', [
            'query' => $query,
            'variables' => $variables,
            'operationName' => 'SearchAds'
        ]);
    }

    public function searchAds(int $categoryId, array $options = []): array
    {
        $params = array_merge($options, [
            'subCategoryId' => $categoryId,
        ]);

        return $this->fetch($params);
    }

    public function getAllCars(array $filters = [], int $limit = 78, int $page = 1): array
    {
        $carsCategoryId = 659;

        $params = [
            'subCategoryId' => $carsCategoryId,
            'filters' => $filters,
            'limit' => $limit,
            'skip' => ($page - 1) * $limit,
            'includeCarsFeatures' => true,
            'includeOwner' => true,
        ];

        return $this->fetch($params);
    }

    public function searchCars(array $filters = []): array
    {
        $carFilters = array_merge([
            [
                'filterId' => 16,
                'features' => [
                    [
                        'featureId' => 1,
                        'optionIds' => [776]
                    ]
                ]
            ]
        ], $filters);

        return $this->getAllCars($carFilters);
    }
}
