<?php

namespace App\Services\Sources\Clients\Enter\Actions;

use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\Enter\Jobs\SearchEnterCategoryJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class OrchestrateEnterSearchAction
{
    /**
     * @param EnterSearchParam[] $params empty array - search by all categories
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

    public function dispatch(): void
    {
        $params = $this->getParams();

        foreach ($params as $param) {
            SearchEnterCategoryJob::dispatch($param);
        }
    }

    /**
     * @throws Throwable
     */
    public function dispatchBatch(): Batch
    {
        $jobs = collect($this->getParams())
            ->map(fn (EnterSearchParam $param) => new SearchEnterCategoryJob($param))
            ->all();

        return Bus::batch($jobs)
            ->name('Enter search: ' . now()->toDateTimeString())
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
    public function dispatchSync(): void
    {
        $params = $this->getParams();

        foreach ($params as $param) {
            dump('Processing: ' .$param->value);
            try {
                (new SearchEnterEntitiesAction($param))->handle();
            } catch (Throwable $e) {
                Log::channel('sources.entity')->error("Failed to search Enter category [{$param->value}]: {$e->getMessage()}", [
                    'exception' => $e,
                ]);

                throw $e;
            }
        }
    }

    private function getParams(): array
    {
        return empty($this->params)
            ? EnterSearchParam::cases()
            : $this->params;
    }
}
