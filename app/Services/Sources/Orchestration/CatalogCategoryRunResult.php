<?php

namespace App\Services\Sources\Orchestration;

use Throwable;

readonly class CatalogCategoryRunResult
{
    public function __construct(
        public CatalogSource $source,
        public string $category,
        public int $durationMs,
        public ?Throwable $exception = null,
    ) {}

    public function succeeded(): bool
    {
        return $this->exception === null;
    }
}
