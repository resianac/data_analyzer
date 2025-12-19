<?php

namespace App\Services\Sources\Contracts;

use App\Services\Sources\Enums\SourceDriverType;

interface DriverInterface
{
    public function getName(): SourceDriverType;
//    public function setConfig(array $config): void;
    public function execute(string $method, string $endpoint, array $data = []): mixed;
//    public function supports(string $type): bool;
}
