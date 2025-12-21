<?php

namespace App\Services\Sources\Repository;

use App\Models\Entity;
use App\Services\Sources\Data\EntityData;
use Illuminate\Support\Collection;

class EntityRepository
{
    /**
     * @param Collection<EntityData> $entities
     * @return void
     */
    public function storeMany(Collection $entities): void
    {
        foreach ($entities as $entity) {
            Entity::updateOrCreate(
                [
                    'external_id' => $entity->external_id,
                    'source' => $entity->source
                ],
                $entity->toArray()
            );
        }
    }

}
