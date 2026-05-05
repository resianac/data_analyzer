<?php

namespace App\Jobs;

use DefStudio\Telegraph\Facades\Telegraph;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendMessageToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    public $timeout = 30;
    public $backoff = 5;

    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        sleep(rand(4, 5));

        try {
            $response = Telegraph::markdown($this->message)->send();

            if (!$response->ok()) {
                Log::channel('telegram.error')->error(
                    "Attempt: {$this->attempts()} | Telegram message failed",
                    ['response' => $response]
                );

                throw new Exception('Telegram API error');
            }
        } catch (Exception $e) {
            Log::channel('telegram.error')->error(
                "Attempt: {$this->attempts()} | Telegram message exception",
                ['message' => $e->getMessage()]
            );

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Log::channel('telegram.error')->critical('Telegram job has failed finally!', [
            'message' => $this->message,
            'error' => $e->getMessage(),
        ]);
    }
}
