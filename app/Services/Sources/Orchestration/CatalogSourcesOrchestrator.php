<?php

namespace App\Services\Sources\Orchestration;

use App\Services\Sources\Clients\Cactus\Actions\OrchestrateCactusSearchAction;
use App\Services\Sources\Clients\Enter\Actions\OrchestrateEnterSearchAction;
use App\Services\Sources\Clients\Maximum\Actions\OrchestrateMaximumSearchAction;
use App\Services\Sources\Clients\Ultra\Actions\OrchestrateUltraSearchAction;
use Closure;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogSourcesOrchestrator
{
    /**
     * @param  array<int, CatalogSource>  $sources
     * @param  null|Closure(CatalogSource): void  $onSourceStart
     * @param  null|Closure(CatalogCategoryRunResult): void  $onCategoryFinished
     * @return array<int, CatalogSourceRunResult>
     */
    public function runSync(
        array $sources,
        string $runId,
        ?Closure $onSourceStart = null,
        ?Closure $onCategoryFinished = null,
    ): array {
        $results = [];

        Log::channel('sources.run')->info('Catalog sources sync run started', [
            'run_id' => $runId,
            'mode' => SourceRunMode::SYNC->value,
            'sources' => array_map(fn (CatalogSource $source) => $source->value, $sources),
        ]);

        foreach ($sources as $source) {
            $onSourceStart?->__invoke($source);
            $startedAt = hrtime(true);
            $categoryResults = $this->sourceOrchestrator($source)->dispatchSync(
                onCategoryFinished: function (CatalogCategoryRunResult $categoryResult) use (
                    $runId,
                    $onCategoryFinished,
                ): void {
                    Log::channel('sources.run')->log(
                        $categoryResult->succeeded() ? 'info' : 'error',
                        $categoryResult->succeeded()
                            ? 'Catalog category completed'
                            : 'Catalog category failed',
                        [
                            'run_id' => $runId,
                            'mode' => SourceRunMode::SYNC->value,
                            'source' => $categoryResult->source->value,
                            'category' => $categoryResult->category,
                            'status' => $categoryResult->succeeded() ? 'completed' : 'failed',
                            'duration_ms' => $categoryResult->durationMs,
                            'exception' => $categoryResult->exception,
                        ],
                    );

                    $onCategoryFinished?->__invoke($categoryResult);
                },
                continueOnError: true,
            );

            $exception = collect($categoryResults)
                ->first(fn (CatalogCategoryRunResult $result) => ! $result->succeeded())
                ?->exception;

            $result = new CatalogSourceRunResult(
                source: $source,
                durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
                exception: $exception,
                categories: $categoryResults,
            );

            $results[] = $result;

            Log::channel('sources.run')->log(
                $result->succeeded() ? 'info' : 'error',
                $result->succeeded() ? 'Catalog source completed' : 'Catalog source failed',
                [
                    'run_id' => $runId,
                    'mode' => SourceRunMode::SYNC->value,
                    'source' => $source->value,
                    'status' => $result->succeeded() ? 'completed' : 'failed',
                    'duration_ms' => $result->durationMs,
                    'exception' => $exception,
                ],
            );
        }

        $failed = count(array_filter($results, fn (CatalogSourceRunResult $result) => ! $result->succeeded()));

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

    /**
     * @param  array<int, CatalogSource>  $sources
     *
     * @throws Throwable
     */
    public function dispatchBatch(
        array $sources,
        string $runId,
        string $connection = 'sources',
        string $queue = 'sources',
    ): Batch {
        $jobs = collect($sources)
            ->flatMap(fn (CatalogSource $source) => $this->sourceOrchestrator($source)->jobs(
                runId: $runId,
                connection: $connection,
                queue: $queue,
            ))
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
            ->then(function (Batch $batch) use ($runId) {
                Log::channel('sources.run')->info('Catalog sources batch completed', [
                    'run_id' => $runId,
                    'batch_id' => $batch->id,
                    'total_jobs' => $batch->totalJobs,
                ]);
            })
            ->catch(function (Batch $batch, Throwable $error) use ($runId) {
                Log::channel('sources.run')->error('Catalog sources batch has failures', [
                    'run_id' => $runId,
                    'batch_id' => $batch->id,
                    'failed_jobs' => $batch->failedJobs,
                    'exception' => $error,
                ]);
            })
            ->finally(function (Batch $batch) use ($runId) {
                Log::channel('sources.run')->info('Catalog sources batch finished', [
                    'run_id' => $runId,
                    'batch_id' => $batch->id,
                    'total_jobs' => $batch->totalJobs,
                    'failed_jobs' => $batch->failedJobs,
                    'pending_jobs' => $batch->pendingJobs,
                ]);
            })
            ->dispatch();
    }

    private function sourceOrchestrator(CatalogSource $source): object
    {
        return match ($source) {
            CatalogSource::ENTER => OrchestrateEnterSearchAction::all(),
            CatalogSource::CACTUS => OrchestrateCactusSearchAction::all(),
            CatalogSource::MAXIMUM => OrchestrateMaximumSearchAction::all(),
            CatalogSource::ULTRA => OrchestrateUltraSearchAction::all(),
        };
    }
}
