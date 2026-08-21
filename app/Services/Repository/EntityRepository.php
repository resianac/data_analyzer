<?php

namespace App\Services\Repository;

use App\Models\Entity;
use App\Services\Sources\Data\EntityData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class EntityRepository
{
    public function __construct(
        private ?MetricTracker $metricTracker = null
    ) {}

    /**
     * Checks the given entity data against the database for duplicates based on specified fields.
     *
     * @param EntityData $entityData
     * @param array $fields Fields to check for duplicates
     * @return Collection|null
     */
    public function filterNewEntity(EntityData $entityData, array $fields): EntityData|null
    {
        $fieldDoesNotExist = false;
        $query = Entity::query()
            ->whereSource($entityData->source)
            ->whereFilterType($entityData->filter_type);

        foreach ($fields as $field) {
            $value = $entityData->data[$field] ?? null;

            if (is_null($value)) {
                $fieldDoesNotExist = true;
                break;
            }

            $query->where("data->{$field}", $value);
        }

        $query->where('external_id', '!=', $entityData->external_id);

        if ($query->exists() && !$fieldDoesNotExist) {
//            Log::channel('sources.entity')->debug(
//                "Entity already exists " .
//                json_encode(
//                    $entityData,
//                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
//                ) .
//                "\nEntities were found: " . $query->get()->pluck('external_id')->implode(', ') . "\n"
//            );

            return null;
        }

        return $entityData;
    }

    /**
     * @param Collection<EntityData> $entities
     * @return void
     * @throws Throwable
     */
    public function storeMany(Collection $entities): void
    {
        DB::transaction(function () use ($entities) {
            foreach ($entities as $data) {
                $entityModel = Entity::where('external_id', $data->external_id)
                    ->where('source', $data->source)
                    ->first();

                if (!$entityModel) {
                    $entity = Entity::create($data->toArray());

                    $this->metricTracker->trackForByFields($entity);
                    continue;
                }

                $entityModel->fill($data->toArray());

                if ($entityModel->isDirty()) {
                    $entityModel->save();

                    $this->metricTracker->trackForByFields($entityModel);
                }
            }
        });
    }

    public function getAvgByField(
        Builder $context,
        string $field,
        string $whereType = "",
        string $whereValue = ""
    ): float {
        if (!empty($whereType) && !empty($whereValue)) {
            $context = $context
                ->where("data->{$whereType}", $whereValue)
                ->where("updated_at", ">", now()->startOfHour()->subMinute());
        }

        $avg = $context->avg("data->{$field}");

        return round((float) $avg, 2);
    }

    public function getMostFrequentByField(
        Builder $context,
        string $field,
        string $whereType = "",
        string $whereValue = ""
    ): array {
        if (!empty($whereType) && !empty($whereValue)) {
            $context = $context->where("data->{$whereType}", $whereValue);
        }

        $result = $context
            ->selectRaw("data->>'$.{$field}' as value, COUNT(*) as count")
            ->groupBy("value")
            ->orderByDesc("count")
            ->limit(1)
            ->first();

        if (!$result) {
            return ['value' => null, 'count' => 0];
        }

        return [
            'value' => $result->value,
            'count' => (int) $result->count,
        ];
    }
}
