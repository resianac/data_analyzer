<?php

namespace App\Services\Sources\Contracts;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Enums\SourceDriverType;
use Illuminate\Support\Collection;

interface DriverInterface
{
    public function getName(): SourceDriverType;
    public function setConfig(BaseConfig $config): static;
    public function call(...$params): Collection;
//    public function setConfig(array $config): void;
//    public function execute(string $method, string $endpoint, array $data = []): mixed;
//    public function supports(string $type): bool;
}
