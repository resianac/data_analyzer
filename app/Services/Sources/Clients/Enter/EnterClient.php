<?php

namespace App\Services\Sources\Clients\Enter;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\Enter\Data\EnterData;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\RabotaMd\Data\JobData;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class EnterClient extends BaseClient
{
    protected string $name = 'enter';
    protected SourceClientType $type = SourceClientType::ENTER;

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
     * @param EnterSearchParam $operationName
     * @param string $page
     * @return Collection<EntityData>
     */
    public function search(EntityFilter $filter, EnterSearchParam $operationName, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        dump("{$operationName->value}?page=$page");

        $data = $this->execute(
            "{$operationName->value}?page=$page",
            $variableClass::byItems()
        );

        $this->hasNextPage = (bool) $data['next_page_button'];

        return EnterData::collect($data['entities'], Collection::class)
            ->map(
                fn (EnterData $enterData) => $enterData->toGeneral($filter, $operationName)
            );
    }
}
