<?php

namespace App\Listeners;

use App\Events\EntityUpdated;
use App\Jobs\SendMessageToTelegram;
use App\Services\Sources\Clients\Marketplace999\Filters\Formatters\FlatDefaultFormatter;
use App\Services\Sources\Filters\Factories\FormatterFactory;
use Illuminate\Support\Facades\Log;

class SendUpdatedEntityListener
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
    public function handle(EntityUpdated $event): void
    {
        $entity = $event->entity;
        $changes = $event->changes;
        $original = $event->original;

        /** @var FlatDefaultFormatter $formatter */
        $formatter = (new FormatterFactory())->make(
            $entity->source,
            $entity->filter_type->value,
            $entity,
            $changes,
            $original,
        );

        if (!$formatter->getWatchedChanges()) {
            return;
        }

        Log::channel('sources.entity')->debug(
            "External ID: {$entity->external_id}. Watched changes were fixed ".
            json_encode(
                $formatter->getWatchedChanges(),
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        );

        $message = $formatter->get();

        SendMessageToTelegram::dispatch($message);
    }
}
