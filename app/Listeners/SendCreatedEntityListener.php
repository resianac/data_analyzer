<?php

namespace App\Listeners;

use App\Events\EntityCreated;
use App\Jobs\SendMessageToTelegram;
use App\Services\Telegram\Formatter;

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
        $f = $event->entity;

        SendMessageToTelegram::dispatch(
            Formatter::makeMarkdown($f)
        );
    }
}
