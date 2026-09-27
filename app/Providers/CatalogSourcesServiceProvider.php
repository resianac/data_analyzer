<?php

namespace App\Providers;

use App\Services\Sources\Modules\Catalog\Application\Run\CatalogProviderRegistry;
use App\Services\Sources\Modules\Catalog\Contracts\CatalogEntitySink;
use App\Services\Sources\Modules\Catalog\Infrastructure\Persistence\EloquentCatalogEntitySink;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\CactusCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\EnterCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\MaximumCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\UltraCatalogProvider;
use Illuminate\Support\ServiceProvider;

class CatalogSourcesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CatalogEntitySink::class, EloquentCatalogEntitySink::class);

        $this->app->singleton(CatalogProviderRegistry::class, fn ($app) => new CatalogProviderRegistry([
            $app->make(EnterCatalogProvider::class),
            $app->make(CactusCatalogProvider::class),
            $app->make(MaximumCatalogProvider::class),
            $app->make(UltraCatalogProvider::class),
        ]));
    }
}
