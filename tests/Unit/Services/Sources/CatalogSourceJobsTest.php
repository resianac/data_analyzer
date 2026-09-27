<?php

use App\Services\Sources\Modules\Catalog\Application\Run\CatalogProviderRegistry;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\CactusCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Cactus\Jobs\SearchCactusCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\EnterCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Enter\Jobs\SearchEnterCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\Jobs\SearchMaximumCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\MaximumCatalogProvider;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\Jobs\SearchUltraCategoryJob;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Ultra\UltraCatalogProvider;

it('builds one queued job for every configured catalog category', function () {
    $runId = 'test-run';

    $registry = new CatalogProviderRegistry([
        new EnterCatalogProvider,
        new CactusCatalogProvider,
        new MaximumCatalogProvider,
        new UltraCatalogProvider,
    ]);

    $jobs = collect($registry->all())
        ->flatMap(fn ($provider) => collect($provider->categories())
            ->map(fn ($category) => $provider->job($category, $runId)
                ->onConnection('sources')
                ->onQueue('sources')))
        ->all();

    expect($jobs)
        ->toHaveCount(6)
        ->and($jobs[0])->toBeInstanceOf(SearchEnterCategoryJob::class)
        ->and($jobs[1])->toBeInstanceOf(SearchEnterCategoryJob::class)
        ->and($jobs[2])->toBeInstanceOf(SearchCactusCategoryJob::class)
        ->and($jobs[3])->toBeInstanceOf(SearchMaximumCategoryJob::class)
        ->and($jobs[4])->toBeInstanceOf(SearchUltraCategoryJob::class)
        ->and($jobs[5])->toBeInstanceOf(SearchUltraCategoryJob::class);

    foreach ($jobs as $job) {
        expect($job->runId)
            ->toBe($runId)
            ->and($job->connection)->toBe('sources')
            ->and($job->queue)->toBe('sources')
            ->and($job->tries)->toBe(3)
            ->and($job->timeout)->toBe(900);
    }
});
