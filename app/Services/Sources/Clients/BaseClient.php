<?php

namespace App\Services\Sources\Clients;

use App\Services\Sources\Contracts\ConfigInterface;
use App\Services\Sources\Contracts\DriverInterface;
use App\Services\Sources\Contracts\SourceClientInterface;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Support\Str;

abstract class BaseClient implements SourceClientInterface
{
    protected DriverInterface $driver;
    protected ConfigInterface $config;
    protected string $name;
    protected SourceClientType $type;

    public function __construct(DriverInterface $driver = null, ConfigInterface $config = null)
    {
        $driverClass = "App\\Services\\Sources\\Drivers\\" . Str::studly($this->name);
        $configClass = "App\\Services\\Sources\\Configs\\" . Str::studly($this->name);

        $this->driver = $driver ?? new $driverClass();
        $this->config = $config ?? new $configClass();
    }

    public function getName(): string
    {
        return $this->name;
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

    abstract public function fetch(array $params = []): mixed;
}
