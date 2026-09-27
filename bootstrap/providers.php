<?php

use Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\CatalogSourcesServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    SchemalessAttributesServiceProvider::class,
];
