<?php

namespace App\Services\Sources\Support\Actions;

use App\Services\Sources\Orchestration\CatalogCategoryRunResult;
use App\Services\Sources\Orchestration\CatalogSource;
use BackedEnum;
use Closure;
use Illuminate\Support\Facades\Log;
use Throwable;
use UnitEnum;

trait RunsCatalogCategoriesSynchronously
{
    /**
     * @param CatalogSource $source
     * @param array<int, UnitEnum> $params
     * @param Closure(UnitEnum): void $handler
     * @param null|Closure(CatalogCategoryRunResult): void $onCategoryFinished
     * @param bool $continueOnError
     * @return array<int, CatalogCategoryRunResult>
     *
     * @throws Throwable
     */
    protected function runCategoriesSync(
        CatalogSource $source,
        array $params,
        Closure $handler,
        ?Closure $onCategoryFinished = null,
        bool $continueOnError = false,
    ): array {
        $results = [];

        foreach ($params as $param) {
            $startedAt = hrtime(true);
            $exception = null;

            try {
                $handler($param);
            } catch (Throwable $error) {
                $exception = $error;

                Log::channel('sources.entity')->error(
                    "Failed to search {$source->label()} category [{$this->categoryValue($param)}]: {$error->getMessage()}",
                    ['exception' => $error],
                );
            }

            $result = new CatalogCategoryRunResult(
                source: $source,
                category: $param->name,
                durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
                exception: $exception,
            );

            $results[] = $result;
            $onCategoryFinished?->__invoke($result);

            if ($exception !== null && ! $continueOnError) {
                throw $exception;
            }
        }

        return $results;
    }

    private function categoryValue(UnitEnum $param): string
    {
        return $param instanceof BackedEnum ? (string) $param->value : $param->name;
    }
}
