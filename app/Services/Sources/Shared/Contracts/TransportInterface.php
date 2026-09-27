<?php

namespace App\Services\Sources\Shared\Contracts;

use App\Services\Sources\Shared\Configuration\BaseSourceConfig;
use App\Services\Sources\Shared\Enums\SourceDriverType;
use Illuminate\Support\Collection;

interface TransportInterface
{
    public function getName(): SourceDriverType;

    public function setConfig(BaseSourceConfig $config): static;

    public function call(string $operation, array $parameters): Collection;
}
