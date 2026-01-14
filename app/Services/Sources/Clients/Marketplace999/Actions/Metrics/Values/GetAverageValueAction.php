<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values;

use App\Models\Entity;
use App\Services\Repository\EntityRepository;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Database\Eloquent\Builder;

class GetAverageValueAction
{
    public function handle(
        string $field,
        string $whereField = "",
        string $whereValue = "",
        ?Builder $context = null
    ): float {
        $query = $context ?? Entity::whereSource(SourceClientType::MARKETPLACE999)
            ->whereFilterType(EntityFilter::FLAT_DEFAULT);

        return (new EntityRepository())->getAvgByField(
            $query,
            $field,
            $whereField, $whereValue,
        );
    }
}
