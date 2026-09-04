<?php

namespace App\Services\Sources\Clients\Bomba\Actions;

use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class OrchestrateBombaSearchAction
{
    /**
     * @param BombaSearchParam[] $params empty array - search by all params
     */
    public function __construct(
        private array $params = [],
    ) {}

    public static function all(): self
    {
        return new self(BombaSearchParam::cases());
    }

    public static function only(BombaSearchParam ...$params): self
    {
        return new self($params);
    }

    public function dispatch(): void
    {
        $params = $this->getParams();

        foreach ($params as $param) {
            // TODO: job
        }
    }

    /**
     * @throws Throwable
     */
    public function dispatchBatch(): Batch
    {
        $jobs = collect($this->getParams());
            //->map(fn (TestSearchParam $params) => TODO: job)
            //->all();

        return Bus::batch($jobs)
            ->name('Bomba search: ' . now()->toDateTimeString())
            ->allowFailures()
            ->then(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Bomba search batch completed [{$batch->id}]: {$batch->totalJobs} params processed."
                );
            })
            ->catch(function (Batch $batch, Throwable $e) {
                Log::channel('sources.entity')->error(
                    "Bomba search batch [{$batch->id}] has failures: {$e->getMessage()}",
                    ['exception' => $e]
                );
            })
            ->finally(function (Batch $batch) {
                Log::channel('sources.entity')->info(
                    "Bomba search batch [{$batch->id}] finished. Failed: {$batch->failedJobs}/{$batch->totalJobs}."
                );
            })
            ->dispatch();
    }

    /**
     * @throws Throwable
     */
    public function dispatchSync(): void
    {
        foreach ($this->getParams() as $param) {
            dump('Processing: ' .$param->value);
            try {
                (new SearchBombaEntitiesAction($param))->handle();
            } catch (Throwable $e) {
                Log::channel('sources.entity')->error(
                    "Failed to search Bomba param [{$param->value}]: {$e->getMessage()}",
                    ['exception' => $e]
                );

                throw $e;
            }
        }
    }

    private function getParams(): array
    {
        return empty($this->params)
            ? BombaSearchParam::cases()
            : $this->params;
    }
}
