<?php

namespace App\Jobs;

use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(rand(3, 4));
        try {
            $response = Telegraph::markdown($this->message)->send();
            if (!$response->ok()) {
                Log::error('Telegram message failed', ['response' => $response]);
            }
        } catch (\Exception $e) {
            Log::error('Telegram message exception', ['message' => $e->getMessage()]);
        }
    }
}
