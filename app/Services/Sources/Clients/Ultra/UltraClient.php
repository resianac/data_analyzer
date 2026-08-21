<?php

namespace App\Services\Sources\Clients\Ultra;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class UltraClient extends BaseClient
{
    protected string $name = 'ultra';
    protected SourceClientType $type = SourceClientType::ULTRA;

    /**
     * @param string $operationName
     * @param array $selectors
     * @return Collection
     */
    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->driver->call($operationName, $selectors);
    }

    /**
     * @param EntityFilter $filter
     * @param UltraSearchParam $param
     * @param string $page
     * @return Collection<EntityData>
     */
    public function search(EntityFilter $filter, UltraSearchParam $param, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        dump("{$param->value}?page=$page");

        $data = $this->execute(
            "{$param->value}?page=$page",
            $variableClass::byItems()
        );

        $this->hasNextPage = !is_null($data['next_page_button']);

        return UltraData::collect($data['entities'], Collection::class)
            ->map(
                fn (UltraData $ultraData) => $ultraData->toGeneral($filter, $param)
            )
            ->filter()
            ->values();
    }
}
