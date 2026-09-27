<?php

namespace App\Services\Sources\Shared\Data;

use Illuminate\Support\Collection;

readonly class PageResult
{
    public function __construct(
        public Collection $entities,
        public bool $hasNextPage,
    ) {}
}
