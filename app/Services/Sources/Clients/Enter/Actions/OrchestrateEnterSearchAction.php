<?php

namespace App\Services\Sources\Clients\Enter\Actions;

use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\Enter\Jobs\SearchEnterCategoryJob;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Support\Actions\RunsCatalogCategoriesSynchronously;
use Closure;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class OrchestrateEnterSearchAction
{
    use RunsCatalogCategoriesSynchronously;

    /**
     * @param  EnterSearchParam[]  $params  empty array - search by all categories
     */
    public function __construct(
        private array $params = [],
    ) {}

    public static function all(): self
    {
        return new self(EnterSearchParam::cases());
    }

    public static function only(EnterSearchParam ...$params): self
    {
        return new self($params);
    }

    public function dispatch(
        ?string $runId = null,
        string $connection = 'sources',
        string $queue = 'sources',
    ): void {
        foreach ($this->jobs($runId, $connection, $queue) as $job) {
            Bus::dispatch($job);
        }
    }

    /**
     * @return array<int, SearchEnterCategoryJob>
     */
    public function jobs(
        ?string $runId = null,
        string $connection = 'sources',
        string $queue = 'sources',
    ): array {
        return collect($this->getParams())
            ->map(fn (EnterSearchParam $param) => (new SearchEnterCategoryJob($param, $runId))
                ->onConnection($connection)
                ->onQueue($queue))
            ->all();
    }

    /**
     * @throws Throwable
     */
    public function dispatchBatch(
        ?string $runId = null,
        string $connection = 'sources',
        string $queue = 'sources',
    ): Batch {
        $jobs = $this->jobs($runId, $connection, $queue);

        return Bus::batch($jobs)
            ->name('Enter search: '.now()->toDateTimeString())
            ->allowFailures()
            ->then(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Enter search batch completed [{$batch->id}]: {$batch->totalJobs} categories processed."
                );
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::channel('sources.entity')->error(
                    "Enter search batch [{$batch->id}] has failures: {$e->getMessage()}",
                    ['exception' => $e]
                );
            })
            ->finally(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Enter search batch [{$batch->id}] finished. Failed: {$batch->failedJobs}/{$batch->totalJobs}."
                );
            })
            ->dispatch();
    }

    /**
     * @throws Throwable
     */
    public function dispatchSync(
        ?Closure $onCategoryFinished = null,
        bool $continueOnError = false,
    ): array {
        return $this->runCategoriesSync(
            source: CatalogSource::ENTER,
            params: $this->getParams(),
            handler: fn (EnterSearchParam $param) => (new SearchEnterEntitiesAction($param))->handle(),
            onCategoryFinished: $onCategoryFinished,
            continueOnError: $continueOnError,
        );
    }

    private function getParams(): array
    {
        return empty($this->params)
            ? EnterSearchParam::cases()
            : $this->params;
    }
}
