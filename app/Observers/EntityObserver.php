<?php

namespace App\Observers;

use App\Events\EntityCreated;
use App\Events\EntityUpdated;
use App\Models\Entity;

class EntityObserver
{
    /**
     * Handle the Entity "created" event.
     */
    public function created(Entity $entity): void
    {
        event(new EntityCreated($entity));
    }

    /**
     * Handle the Entity "updated" event.
     */
    public function updated(Entity $entity): void
    {
        event(new EntityUpdated(
            $entity,
            $entity->getChanges(),
            $entity->getOriginal()
        ));
    }

    /**
     * Handle the Entity "deleted" event.
     */
    public function deleted(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "restored" event.
     */
    public function restored(Entity $entity): void
    {
        //
    }

    /**
     * Handle the Entity "force deleted" event.
     */
    public function forceDeleted(Entity $entity): void
    {
        //
    }
}
