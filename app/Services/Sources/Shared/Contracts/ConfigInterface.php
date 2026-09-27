<?php

namespace App\Services\Sources\Shared\Contracts;

use App\Services\Sources\Shared\Enums\EntityFilter;

interface ConfigInterface
{
    public function get(string $key, $default = null);

    public function set(string $key, $value): void;

    public function all(): array;

    public function getFieldsToCheck(EntityFilter $filter): array;
}
