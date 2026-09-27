<?php

namespace App\Services\Sources\Modules\Catalog\Domain\Results;

use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use Throwable;

readonly class SourceRunResult
{
    /**
     * @param  array<int, CategoryRunResult>  $categories
     */
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
