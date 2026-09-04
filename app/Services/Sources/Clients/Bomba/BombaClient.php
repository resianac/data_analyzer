<?php

namespace App\Services\Sources\Clients\Bomba;

use App\Data\EntityData;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Bomba\Data\BombaData;
use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class BombaClient extends BaseClient
{
    protected string $name = 'bomba';
    protected SourceClientType $type = SourceClientType::BOMBA;

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
     * @param BombaSearchParam $param
     * @param string $page
     * @return Collection<EntityData>
     */
    public function search(EntityFilter $filter, BombaSearchParam $param, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        dump("{$param->value}?page=$page");

        $data = $this->execute(
            "{$param->value}?page=$page",
            $variableClass::byItems()
        );

        dd($data);

        $this->hasNextPage = (bool) $data['next_page_button'];

        return BombaData::collect($data['entities'], Collection::class)
            ->map(fn (BombaData $item) => $item->toGeneral($filter));
    }
}
