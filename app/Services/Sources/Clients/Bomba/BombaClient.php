<?php

namespace App\Services\Sources\Clients\Bomba;

use App\Data\EntityData;
use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Bomba\Data\BombaData;
use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class BombaClient extends BaseClient
{
    protected string $name = 'bomba';

    protected SourceClientType $type = SourceClientType::BOMBA;

    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->transport->call($operationName, $selectors);
    }

    /**
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
