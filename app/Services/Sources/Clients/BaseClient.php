<?php

namespace App\Services\Sources\Clients;

use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Contracts\DriverInterface;
use App\Services\Sources\Contracts\SourceClientInterface;
use App\Services\Sources\Drivers\GraphQLDriver;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Support\Str;

abstract class BaseClient implements SourceClientInterface
{
    protected DriverInterface $driver;
    protected ConfigInterface $config;
    protected string $name;
    protected int $count;
    protected bool $hasNextPage = false;
    protected SourceClientType $type;

    public function __construct(DriverInterface $driver, ConfigInterface $config = null)
    {
        $sourceName = Str::studly($this->name);
        $configClass = "App\\Services\\Sources\\Clients\\$sourceName\\{$sourceName}Config";
        $this->config = $config ?? new $configClass();

        $this->setDriver($driver);
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

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @param GraphQLDriver $driver
     */
    public function setDriver(DriverInterface $driver): static
    {
        $this->driver = $driver->setConfig($this->config);

        return $this;
    }
}
