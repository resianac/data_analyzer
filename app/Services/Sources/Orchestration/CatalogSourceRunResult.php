<?php

namespace App\Services\Sources\Orchestration;

use Throwable;

readonly class CatalogSourceRunResult
{
    public function __construct(
        public CatalogSource $source,
        public int $durationMs,
        public ?Throwable $exception = null,
        public array $categories = [],
    ) {}

    public function succeeded(): bool
    {
        return $this->exception === null;
    }
}
