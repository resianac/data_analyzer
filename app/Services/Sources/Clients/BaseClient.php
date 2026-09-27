<?php

namespace App\Services\Sources\Clients;

use App\Services\Sources\Shared\Contracts\ConfigInterface;
use App\Services\Sources\Shared\Contracts\SourceClientInterface;
use App\Services\Sources\Shared\Contracts\TransportInterface;
use App\Services\Sources\Shared\Enums\SourceClientType;
use App\Services\Sources\Shared\Transport\GraphQLTransport;
use Illuminate\Support\Str;

abstract class BaseClient implements SourceClientInterface
{
    protected TransportInterface $transport;

    protected ConfigInterface $config;

    protected string $name;

    protected int $count;

    protected bool $hasNextPage = false;

    protected SourceClientType $type;

    public function __construct(TransportInterface $transport, ?ConfigInterface $config = null)
    {
        $sourceName = Str::studly($this->name);
        $configClass = "App\\Services\\Sources\\Clients\\$sourceName\\{$sourceName}Config";
        $this->config = $config ?? new $configClass;

        $this->setTransport($transport);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntitiesCount(): int
    {
        return $this->count;
    }

    public function hasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    public function getType(): SourceClientType
    {
        return $this->type;
    }

    public function getTransport(): TransportInterface
    {
        return $this->transport;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param  GraphQLTransport  $transport
     */
    public function setTransport(TransportInterface $transport): static
    {
        $this->transport = $transport->setConfig($this->config);

        return $this;
    }
}
