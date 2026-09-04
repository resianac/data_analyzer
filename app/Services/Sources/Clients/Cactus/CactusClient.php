<?php

namespace App\Services\Sources\Clients\Cactus;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Cactus\Data\CactusData;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class CactusClient extends BaseClient
{
    protected string $name = 'cactus';
    protected SourceClientType $type = SourceClientType::CACTUS;

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
     * @param CactusSearchParam $param
     * @param string $page
     * @return Collection<EntityData>
     */
    public function search(EntityFilter $filter, CactusSearchParam $param, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        dump("{$param->value}?page=$page");

        $data = $this->execute(
            "{$param->value}?page_=page_$page",
            $variableClass::byItems()
        );

        $this->hasNextPage = (bool) $data['next_page_button'];

        return CactusData::collect($data['entities'], Collection::class)
            ->map(fn (CactusData $item) => $item->toGeneral($filter, $param))
            ->filter()
            ->values();
    }
}
