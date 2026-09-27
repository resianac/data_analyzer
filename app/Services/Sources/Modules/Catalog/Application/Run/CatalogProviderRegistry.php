<?php

namespace App\Services\Sources\Modules\Catalog\Application\Run;

use App\Services\Sources\Modules\Catalog\Contracts\CatalogProvider;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use InvalidArgumentException;

class CatalogProviderRegistry
{
    /** @var array<string, CatalogProvider> */
    private array $providers = [];

    /**
     * @param  iterable<CatalogProvider>  $providers
     */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->source()->value] = $provider;
        }
    }

    public function get(CatalogSource $source): CatalogProvider
    {
        return $this->providers[$source->value]
            ?? throw new InvalidArgumentException("Catalog provider [{$source->value}] is not registered.");
    }

    /** @return array<int, CatalogProvider> */
    public function all(): array
    {
        return array_values($this->providers);
    }
}
