<?php

namespace App\Services\Sources\Clients\Ultra;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Shared\Data\PageResult;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class UltraClient extends BaseClient
{
    protected string $name = 'ultra';

    protected SourceClientType $type = SourceClientType::ULTRA;

    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->transport->call($operationName, $selectors);
    }

    public function search(EntityFilter $filter, UltraSearchParam $param, string $page): PageResult
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        $data = $this->execute(
            "{$param->value}?page=$page",
            $variableClass::byItems()
        );

        return new PageResult(
            entities: UltraData::collect($data['entities'], Collection::class)
                ->map(fn (UltraData $ultraData) => $ultraData->toGeneral($filter, $param))
                ->filter()
                ->values(),
            hasNextPage: ! is_null($data['next_page_button']),
        );
    }
}
