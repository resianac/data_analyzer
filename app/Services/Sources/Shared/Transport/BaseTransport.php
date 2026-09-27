<?php

namespace App\Services\Sources\Shared\Transport;

use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use App\Services\Sources\Shared\Contracts\TransportInterface;
use App\Services\Sources\Shared\Enums\SourceDriverType;
use Illuminate\Http\Client\PendingRequest;

abstract class BaseTransport implements TransportInterface
{
    protected SourceDriverType $name;

    protected BaseSourceConfig $config;

    protected ?PendingRequest $client = null;

    public function __construct(?BaseSourceConfig $config = null)
    {
        if ($config) {
            $this->config = $config;
        }
    }

    public static function make(?BaseSourceConfig $config = null): static
    {
        return new static($config);
    }

    public function getName(): SourceDriverType
    {
        return $this->name;
    }

    public function setConfig(BaseSourceConfig $config): static
    {
        $this->config = $config;

        return $this;
    }

    abstract protected function initializeClient(): void;
}
