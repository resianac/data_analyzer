<?php

namespace App\Services\Sources\Clients\Enter;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Enter\Data\EnterData;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Shared\Data\PageResult;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Factories\VariableFactory;
use Illuminate\Support\Collection;

class EnterClient extends BaseClient
{
    protected string $name = 'enter';

    protected SourceClientType $type = SourceClientType::ENTER;

    public function execute(string $operationName, array $selectors): Collection
    {
        return $this->transport->call($operationName, $selectors);
    }

    public function search(EntityFilter $filter, EnterSearchParam $operationName, string $page): PageResult
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        $data = $this->execute(
            "{$operationName->value}?page=$page",
            $variableClass::byItems()
        );

        return new PageResult(
            entities: EnterData::collect($data['entities'], Collection::class)
                ->map(fn (EnterData $enterData) => $enterData->toGeneral($filter, $operationName)),
            hasNextPage: (bool) $data['next_page_button'],
        );
    }
}
