<?php

namespace App\Services\Sources\Clients\Cactus;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Cactus\Data\CactusData;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Shared\Data\PageResult;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class CactusClient extends BaseClient
{
    protected string $name = 'cactus';

    protected SourceClientType $type = SourceClientType::CACTUS;

    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->transport->call($operationName, $selectors);
    }

    public function search(EntityFilter $filter, CactusSearchParam $param, string $page): PageResult
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        $data = $this->execute(
            "{$param->value}?page_=page_$page",
            $variableClass::byItems()
        );

        return new PageResult(
            entities: CactusData::collect($data['entities'], Collection::class)
                ->map(fn (CactusData $item) => $item->toGeneral($filter, $param))
                ->filter()
                ->values(),
            hasNextPage: (bool) $data['next_page_button'],
        );
    }
}
