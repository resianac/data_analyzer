<?php

namespace App\Services\Sources\Contracts;

use App\Services\Sources\Enums\SourceClientType;

interface SourceClientInterface
{
    public function getName(): string;
    public function getEntitiesCount(): int;
    public function getType(): SourceClientType;

    public function getDriver(): DriverInterface;
    public function setDriver(DriverInterface $driver): static;

    public function getConfig(): ConfigInterface;
//    public function isAvailable(): bool;
//    public function testConnection(): bool;
}
