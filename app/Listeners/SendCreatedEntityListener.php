<?php

namespace App\Listeners;

use App\Events\EntityCreated;
use App\Jobs\SendMessageToTelegram;
use App\Services\Sources\Shared\Factories\FormatterFactory;

class SendCreatedEntityListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EntityCreated $event): void
    {
        $entity = $event->entity;

        $formatter = (new FormatterFactory())->make(
            $entity->source,
            $entity->filter_type,
            $entity
        );

        $message = $formatter->get();

        SendMessageToTelegram::dispatch($message);
    }
}
