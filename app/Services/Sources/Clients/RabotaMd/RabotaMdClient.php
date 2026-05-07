<?php

namespace App\Services\Sources\Clients\RabotaMd;

use App\Services\Sources\Clients\BaseClient;
use App\Services\Sources\Clients\RabotaMd\Data\JobData;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\VariableFactory;
use Illuminate\Support\Collection;

class RabotaMdClient extends BaseClient
{
    protected string $name = 'rabota_md';
    protected SourceClientType $type = SourceClientType::RABOTA_MD;

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
     * @param string $operationName
     * @param string $page
     * @return Collection<EntityData>
     */
    public function jobSearch(EntityFilter $filter, string $operationName, string $page): Collection
    {
        $variableClass = (new VariableFactory)->make($this->type, $filter);

        $data = $this->execute(
            "$operationName/page-$page",
            $variableClass::byItems()
        );

        $vacancies = $data['vacancy_items'];
        $this->count = !!$data['next_page_button'];

        return JobData::collect($vacancies, Collection::class)
            ->map(
                fn (JobData $jobData) => $jobData->toGeneral($filter)
            );
    }
}
