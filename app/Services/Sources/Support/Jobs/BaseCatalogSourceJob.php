<?php

namespace App\Services\Sources\Support\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseCatalogSourceJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 900;

    public bool $failOnTimeout = true;

    public function __construct(public readonly ?string $runId = null) {}

    final public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $startedAt = hrtime(true);
        $this->writeLog('info', 'Catalog source job started');

        try {
            $this->runSearch();

            $this->writeLog('info', 'Catalog source job completed', [
                'duration_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
            ]);
        } catch (Throwable $error) {
            $this->writeLog('error', 'Catalog source job attempt failed', [
                'duration_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
                'exception' => $error,
            ]);

            throw $error;
        }
    }

    final public function backoff(): array
    {
        return [60, 300];
    }

    final public function failed(?Throwable $error): void
    {
        $this->writeLog('critical', 'Catalog source job failed permanently', [
            'exception' => $error,
        ]);
    }

    final public function tags(): array
    {
        return [
            'catalog-source:'.$this->source(),
            'category:'.$this->category(),
            'run:'.($this->runId ?? 'standalone'),
        ];
    }

    abstract protected function source(): string;

    abstract protected function category(): string;

    abstract protected function runSearch(): void;

    private function writeLog(string $level, string $message, array $context = []): void
    {
        Log::channel('sources.run')->log($level, $message, [
            'run_id' => $this->runId,
            'source' => $this->source(),
            'category' => $this->category(),
            'attempt' => $this->attempts(),
            ...$context,
        ]);
    }
}
