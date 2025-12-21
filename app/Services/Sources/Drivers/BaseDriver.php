<?php

namespace App\Services\Sources\Drivers;

use App\Services\Sources\Configs\BaseConfig;
use App\Services\Sources\Contracts\DriverInterface;
use App\Services\Sources\Enums\SourceDriverType;
use Illuminate\Http\Client\PendingRequest;

abstract class BaseDriver implements DriverInterface
{
    protected SourceDriverType $name;
    protected BaseConfig $config;
    protected ?PendingRequest $client = null;

    public function __construct(BaseConfig $config = null)
    {
        if ($config) {
            $this->config = $config;
        }
    }

    public static function make(BaseConfig $config = null): static
    {
        return new static($config);
    }

    public function getName(): SourceDriverType
    {
        return $this->name;
    }

    public function setConfig(BaseConfig $config): static
    {
        $this->config = $config;

        return $this;
    }

    abstract protected function initializeClient(): void;
//    abstract public function execute(string $method, string $endpoint, array $data = []): mixed;
}
