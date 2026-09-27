<?php

namespace App\Console\Commands\Sources\Catalog;

use App\Services\Sources\Orchestration\CatalogCategoryRunResult;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Orchestration\CatalogSourcesOrchestrator;
use App\Services\Sources\Orchestration\SourceRunMode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class RunCatalogSourcesCommand extends Command
{
    protected $signature = 'sources:catalog:run
        {sources?* : Sources to run: enter, cactus, maximum, ultra}
        {--mode=sync : Execution mode: sync or queue}
        {--connection=sources : Queue connection for queue mode}
        {--queue=sources : Queue name for queue mode}';

    protected $description = 'Run catalog data sources now or dispatch them as one queued batch';

    public function handle(CatalogSourcesOrchestrator $orchestrator): int
    {
        $mode = SourceRunMode::tryFrom(strtolower((string) $this->option('mode')));

        if ($mode === null) {
            $this->components->error('Invalid mode. Allowed values: sync, queue.');

            return self::INVALID;
        }

        $sources = $this->resolveSources((array) $this->argument('sources'));

        if ($sources === null) {
            return self::INVALID;
        }

        $connection = trim((string) $this->option('connection'));
        $queue = trim((string) $this->option('queue'));

        if ($mode === SourceRunMode::QUEUE && ($connection === '' || $queue === '')) {
            $this->components->error('Queue connection and queue name cannot be empty.');

            return self::INVALID;
        }

        $runId = (string) Str::ulid();

        $this->newLine();
        $this->components->info('Catalog sources run');
        $this->line("Run ID: <comment>{$runId}</comment>");
        $this->line("Mode: <comment>{$mode->value}</comment>");
        $this->line('Sources: <comment>'.implode(', ', array_map(
            fn (CatalogSource $source) => $source->value,
            $sources,
        )).'</comment>');

        return match ($mode) {
            SourceRunMode::SYNC => $this->runSync($orchestrator, $sources, $runId),
            SourceRunMode::QUEUE => $this->runQueued(
                $orchestrator,
                $sources,
                $runId,
                $connection,
                $queue,
            ),
        };
    }

    /**
     * @param  array<int, string>  $requestedSources
     * @return null|array<int, CatalogSource>
     */
    private function resolveSources(array $requestedSources): ?array
    {
        if ($requestedSources === []) {
            return CatalogSource::cases();
        }

        $sources = [];

        foreach ($requestedSources as $requestedSource) {
            $value = strtolower(trim((string) $requestedSource));
            $source = CatalogSource::tryFrom($value);

            if ($source === null) {
                $allowed = implode(', ', array_column(CatalogSource::cases(), 'value'));
                $this->components->error("Unknown source [{$requestedSource}]. Allowed: {$allowed}.");

                return null;
            }

            $sources[$source->value] = $source;
        }

        return array_values($sources);
    }

    /**
     * @param  array<int, CatalogSource>  $sources
     */
    private function runSync(
        CatalogSourcesOrchestrator $orchestrator,
        array $sources,
        string $runId,
    ): int {
        $position = 0;
        $totalSources = count($sources);

        $results = $orchestrator->runSync(
            sources: $sources,
            runId: $runId,
            onSourceStart: function (CatalogSource $source) use (&$position, $totalSources): void {
                $position++;
                $this->newLine();
                $this->line(sprintf('[%d/%d] %s', $position, $totalSources, $source->label()));
            },
            onCategoryFinished: fn (CatalogCategoryRunResult $result) => $this->renderCategoryResult($result),
        );

        $categoryResults = collect($results)
            ->flatMap(fn ($result) => $result->categories);
        $failed = $categoryResults
            ->filter(fn (CatalogCategoryRunResult $result) => ! $result->succeeded())
            ->count();
        $completed = $categoryResults->count() - $failed;

        if ($failed > 0) {
            $this->newLine();
            $this->components->error(sprintf(
                'Run finished with errors: %d categories failed, %d completed.',
                $failed,
                $completed,
            ));

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->success(sprintf(
            'All %d sources and %d categories completed.',
            count($results),
            $completed,
        ));

        return self::SUCCESS;
    }

    /**
     * @param array<int, CatalogSource> $sources
     * @throws Throwable
     */
    private function runQueued(
        CatalogSourcesOrchestrator $orchestrator,
        array $sources,
        string $runId,
        string $connection,
        string $queue,
    ): int {
        $batch = $orchestrator->dispatchBatch(
            sources: $sources,
            runId: $runId,
            connection: $connection,
            queue: $queue,
        );

        $this->line("Connection: <comment>{$connection}</comment>");
        $this->line("Queue: <comment>{$queue}</comment>");
        $this->newLine();
        $this->components->success("Batch {$batch->id} created with {$batch->totalJobs} jobs.");
        $this->line("Worker: <comment>php artisan queue:work {$connection} --queue={$queue} --timeout=900 --tries=3</comment>");

        return self::SUCCESS;
    }

    private function formatDuration(int $milliseconds): string
    {
        return number_format($milliseconds / 1000, 1).'s';
    }

    private function renderCategoryResult(CatalogCategoryRunResult $result): void
    {
        $symbol = $result->succeeded() ? '<fg=green>✓</>' : '<fg=red>✗</>';
        $status = $result->succeeded() ? 'completed' : 'failed';
        $details = $result->exception === null ? '' : '  '.$result->exception->getMessage();

        $this->line(sprintf(
            '  %s %-16s %-10s %8s%s',
            $symbol,
            $result->category,
            $status,
            $this->formatDuration($result->durationMs),
            $details,
        ));
    }
}
