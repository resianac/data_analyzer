<?php

namespace App\Services\Sources\Clients\Maximum;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Maximum\Data\MaximumData;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Shared\Data\PageResult;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class MaximumClient extends BaseClient
{
    protected string $name = 'maximum';

    protected SourceClientType $type = SourceClientType::MAXIMUM;

    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->transport->call($operationName, $selectors);
    }

    public function search(EntityFilter $filter, MaximumSearchParam $param, string $page): PageResult
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        $data = $this->execute(
            "{$param->value}/$page/",
            $variableClass::byItems()
        );

        return new PageResult(
            entities: MaximumData::collect($data['entities'], Collection::class)
                ->map(fn (MaximumData $item) => $item->toGeneral($filter, $param))
                ->filter()
                ->values(),
            hasNextPage: ! is_null($data['next_page_button']),
        );
    }
}
