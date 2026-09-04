<?php

namespace App\Services\Sources\Clients\Maximum;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Maximum\Data\MaximumData;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class MaximumClient extends BaseClient
{
    protected string $name = 'maximum';
    protected SourceClientType $type = SourceClientType::MAXIMUM;

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
     * @param MaximumSearchParam $param
     * @param string $page
     * @return Collection<EntityData>
     */
    public function search(EntityFilter $filter, MaximumSearchParam $param, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        dump("{$param->value}/$page/");

        $data = $this->execute(
            "{$param->value}/$page/",
            $variableClass::byItems()
        );

        $this->hasNextPage = !is_null($data['next_page_button']);

        return MaximumData::collect($data['entities'], Collection::class)
            ->map(fn (MaximumData $item) => $item->toGeneral($filter, $param))
            ->filter()
            ->values();
    }
}
