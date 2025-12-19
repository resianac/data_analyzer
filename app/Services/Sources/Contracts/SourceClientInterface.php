<?php

namespace App\Services\Sources\Contracts;

use App\Services\Sources\Enums\SourceClientType;

interface SourceClientInterface
{
    public function getName(): string;
    public function getType(): SourceClientType;
    public function getDriver(): DriverInterface;
    public function getConfig(): ConfigInterface;
//    public function isAvailable(): bool;
//    public function testConnection(): bool;
    public function fetch(array $params = []): mixed;
}
