<?php

namespace App\Services\Sources\Modules\Catalog\Application\Run;

use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogRunObserver;
use App\Services\Sources\Modules\Catalog\Domain\Results\CategoryRunResult;
use App\Services\Sources\Modules\Catalog\Domain\Results\SourceRunResult;
use App\Services\Sources\Modules\Catalog\Domain\SourceRunMode;
use Illuminate\Support\Facades\Log;
use Throwable;

class CatalogSourceRunner
{
    public function run(
        CatalogProvider $provider,
        string $runId,
        CatalogRunObserver $observer,
    ): SourceRunResult {
        $startedAt = hrtime(true);
        $categoryResults = [];

        foreach ($provider->categories() as $category) {
            $categoryStartedAt = hrtime(true);
            $exception = null;

            try {
                $provider->import($category);
            } catch (Throwable $error) {
                $exception = $error;
            }

            $result = new CategoryRunResult(
                source: $provider->source(),
                category: $category->name,
                durationMs: (int) ((hrtime(true) - $categoryStartedAt) / 1_000_000),
                exception: $exception,
            );

            $categoryResults[] = $result;
            $this->logCategoryResult($runId, $result);
            $observer->categoryFinished($result);
        }

        $exception = collect($categoryResults)
            ->first(fn (CategoryRunResult $result) => ! $result->succeeded())
            ?->exception;

        return new SourceRunResult(
            source: $provider->source(),
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
            exception: $exception,
            categories: $categoryResults,
        );
    }

    private function logCategoryResult(string $runId, CategoryRunResult $result): void
    {
        Log::channel('sources.run')->log(
            $result->succeeded() ? 'info' : 'error',
            $result->succeeded() ? 'Catalog category completed' : 'Catalog category failed',
            [
                'run_id' => $runId,
                'mode' => SourceRunMode::SYNC->value,
                'source' => $result->source->value,
                'category' => $result->category,
                'status' => $result->succeeded() ? 'completed' : 'failed',
                'duration_ms' => $result->durationMs,
                'exception' => $result->exception,
            ],
        );
    }
}
