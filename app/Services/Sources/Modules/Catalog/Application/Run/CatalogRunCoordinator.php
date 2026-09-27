<?php

namespace App\Services\Sources\Modules\Catalog\Application\Run;

use App\Services\Sources\Modules\Catalog\Contracts\CatalogRunObserver;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Domain\Results\SourceRunResult;
use App\Services\Sources\Modules\Catalog\Domain\SourceRunMode;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogRunCoordinator
{
    public function __construct(
        private readonly CatalogProviderRegistry $providers,
        private readonly CatalogSourceRunner $runner,
    ) {}

    /**
     * @param  array<int, CatalogSource>  $sources
     * @return array<int, SourceRunResult>
     */
    public function runSync(
        array $sources,
        string $runId,
        ?CatalogRunObserver $observer = null,
    ): array {
        $observer ??= new NullCatalogRunObserver;
        $results = [];
        $total = count($sources);

        Log::channel('sources.run')->info('Catalog sources sync run started', [
            'run_id' => $runId,
            'mode' => SourceRunMode::SYNC->value,
            'sources' => array_map(fn (CatalogSource $source) => $source->value, $sources),
        ]);

        foreach ($sources as $index => $source) {
            $observer->sourceStarted($source, $index + 1, $total);
            $result = $this->runner->run($this->providers->get($source), $runId, $observer);
            $results[] = $result;
            $this->logSourceResult($runId, $result);
        }

        $failed = count(array_filter($results, fn (SourceRunResult $result) => ! $result->succeeded()));

        Log::channel('sources.run')->log(
            $failed === 0 ? 'info' : 'warning',
            'Catalog sources sync run finished',
            [
                'run_id' => $runId,
                'mode' => SourceRunMode::SYNC->value,
                'completed' => count($results) - $failed,
                'failed' => $failed,
            ],
        );

        return $results;
    }

    /** @param array<int, CatalogSource> $sources */
    public function dispatchBatch(
        array $sources,
        string $runId,
        string $connection = 'sources',
        string $queue = 'sources',
    ): Batch {
        $jobs = collect($sources)
            ->flatMap(function (CatalogSource $source) use ($runId, $connection, $queue) {
                $provider = $this->providers->get($source);

                return collect($provider->categories())->map(function ($category) use (
                    $provider,
                    $runId,
                    $connection,
                    $queue,
                ) {
                    $job = $provider->job($category, $runId);

                    return $job->onConnection($connection)->onQueue($queue);
                });
            })
            ->all();

        Log::channel('sources.run')->info('Catalog sources batch is being dispatched', [
            'run_id' => $runId,
            'mode' => SourceRunMode::QUEUE->value,
            'sources' => array_map(fn (CatalogSource $source) => $source->value, $sources),
            'connection' => $connection,
            'queue' => $queue,
            'jobs' => count($jobs),
        ]);

        return Bus::batch($jobs)
            ->name("Catalog sources [{$runId}]")
            ->allowFailures()
            ->then(static fn (Batch $batch) => self::logBatchCompleted($runId, $batch))
            ->catch(static fn (Batch $batch, Throwable $error) => self::logBatchFailed($runId, $batch, $error))
            ->finally(static fn (Batch $batch) => self::logBatchFinished($runId, $batch))
            ->dispatch();
    }

    private function logSourceResult(string $runId, SourceRunResult $result): void
    {
        Log::channel('sources.run')->log(
            $result->succeeded() ? 'info' : 'error',
            $result->succeeded() ? 'Catalog source completed' : 'Catalog source failed',
            [
                'run_id' => $runId,
                'mode' => SourceRunMode::SYNC->value,
                'source' => $result->source->value,
                'status' => $result->succeeded() ? 'completed' : 'failed',
                'duration_ms' => $result->durationMs,
                'exception' => $result->exception,
            ],
        );
    }

    private static function logBatchCompleted(string $runId, Batch $batch): void
    {
        Log::channel('sources.run')->info('Catalog sources batch completed', [
            'run_id' => $runId,
            'batch_id' => $batch->id,
            'total_jobs' => $batch->totalJobs,
        ]);
    }

    private static function logBatchFailed(string $runId, Batch $batch, Throwable $error): void
    {
        Log::channel('sources.run')->error('Catalog sources batch has failures', [
            'run_id' => $runId,
            'batch_id' => $batch->id,
            'failed_jobs' => $batch->failedJobs,
            'exception' => $error,
        ]);
    }

    private static function logBatchFinished(string $runId, Batch $batch): void
    {
        Log::channel('sources.run')->info('Catalog sources batch finished', [
            'run_id' => $runId,
            'batch_id' => $batch->id,
            'total_jobs' => $batch->totalJobs,
            'failed_jobs' => $batch->failedJobs,
            'pending_jobs' => $batch->pendingJobs,
        ]);
    }
}
