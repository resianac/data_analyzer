<?php

namespace App\Services\Sources\Shared\Contracts;

use App\Services\Sources\Shared\Enums\SourceClientType;

interface SourceClientInterface
{
    public function getName(): string;

    public function getEntitiesCount(): int;

    public function getType(): SourceClientType;

    public function getTransport(): TransportInterface;

    public function setTransport(TransportInterface $transport): static;

    public function getConfig(): ConfigInterface;
    //    public function isAvailable(): bool;
    //    public function testConnection(): bool;
}
