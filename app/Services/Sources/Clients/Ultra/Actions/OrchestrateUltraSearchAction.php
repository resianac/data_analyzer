<?php

namespace App\Services\Sources\Clients\Ultra\Actions;

use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Clients\Ultra\Jobs\SearchUltraCategoryJob;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Support\Actions\RunsCatalogCategoriesSynchronously;
use Closure;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class OrchestrateUltraSearchAction
{
    use RunsCatalogCategoriesSynchronously;

    /**
     * @param  UltraSearchParam[]  $params  empty array - search by all params
     */
    public function __construct(
        private array $params = [],
    ) {}

    public static function all(): self
    {
        return new self(UltraSearchParam::cases());
    }

    public static function only(UltraSearchParam ...$params): self
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
     * @return array<int, SearchUltraCategoryJob>
     */
    public function jobs(
        ?string $runId = null,
        string $connection = 'sources',
        string $queue = 'sources',
    ): array {
        return collect($this->getParams())
            ->map(fn (UltraSearchParam $param) => (new SearchUltraCategoryJob($param, $runId))
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
            ->name('Ultra search: '.now()->toDateTimeString())
            ->allowFailures()
            ->then(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Ultra search batch completed [{$batch->id}]: {$batch->totalJobs} params processed."
                );
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::channel('sources.entity')->error(
                    "Ultra search batch [{$batch->id}] has failures: {$e->getMessage()}",
                    ['exception' => $e]
                );
            })
            ->finally(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Ultra search batch [{$batch->id}] finished. Failed: {$batch->failedJobs}/{$batch->totalJobs}."
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
            source: CatalogSource::ULTRA,
            params: $this->getParams(),
            handler: fn (UltraSearchParam $param) => (new SearchUltraEntitiesAction($param))->handle(),
            onCategoryFinished: $onCategoryFinished,
            continueOnError: $continueOnError,
        );
    }

    private function getParams(): array
    {
        return empty($this->params)
            ? UltraSearchParam::cases()
            : $this->params;
    }
}
