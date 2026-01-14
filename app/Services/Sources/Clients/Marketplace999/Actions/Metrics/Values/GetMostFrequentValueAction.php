<?php

namespace App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values;

use App\Models\Entity;
use App\Services\Repository\EntityRepository;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GetMostFrequentValueAction
{
    /**
     * @param string $field
     * @param string $whereField
     * @param string $whereValue
     * @param Builder|null $context
     * @param Carbon|null $asOf
     * @return array{value: string|null, count: int}
     */
    public function handle(
        string $field,
        string $whereField = "",
        string $whereValue = "",
        ?Builder $context = null,
        ?Carbon $asOf = null
    ): array {
        $query = $context ?? Entity::whereSource(SourceClientType::MARKETPLACE999)
            ->whereFilterType(EntityFilter::FLAT_DEFAULT);

        if ($asOf) {
            $query->where('updated_at', '<', $asOf);
        }

        return (new EntityRepository())->getMostFrequentByField(
            $context,
            $field,
            $whereField, $whereValue,
        );
    }
}
