<?php

namespace App\Services\Sources\Contracts;

interface ConfigInterface
{
    public function get(string $key, $default = null);
    public function set(string $key, $value): void;
    public function all(): array;
}
