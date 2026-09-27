<?php

namespace App\Services\Sources\Modules\Catalog\Domain\Results;

use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use Throwable;

readonly class CategoryRunResult
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
