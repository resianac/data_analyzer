<?php

namespace App\Services\Sources\Drivers;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\DriverInterface;
use App\Services\Sources\Enums\SourceDriverType;

abstract class BaseDriver implements DriverInterface
{
    protected SourceDriverType $name;
    protected BaseConfig $config;

    public function __construct(BaseConfig $config)
    {
        $this->config = $config;
    }

    public function getName(): SourceDriverType
    {
        return $this->name;
    }

    abstract public function execute(string $method, string $endpoint, array $data = []): mixed;
}
