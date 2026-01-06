<?php

namespace App\Services\Repository;

use App\Models\Entity;
use App\Services\Sources\Data\EntityData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EntityRepository
{
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
            Log::channel('sources.entity')->debug(
                "Entity already exists ".
                json_encode(
                    $entityData,
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                ).
                "\nEntities were found: " .  $query->get()->pluck('external_id')->implode(', ') . "\n"
            );

            return null;
        }

        return $entityData;
    }

    /**
     * @param Collection<EntityData> $entities
     * @return void
     */
    public function storeMany(Collection $entities): void
    {
        foreach ($entities as $data) {
            $entityModel = Entity::where('external_id', $data->external_id)
                ->where('source', $data->source)
                ->first();

            if (!$entityModel) {
                Entity::create($data->toArray());
                continue;
            }

            $entityModel->fill($data->toArray());

            if ($entityModel->isDirty()) {
                $entityModel->save();
            }

//            Entity::updateOrCreate(
//                [
//                    'external_id' => $entity->external_id,
//                    'source' => $entity->source
//                ],
//                $entity->toArray()
//            );
        }
    }

}
