<?php

use App\Services\Sources\Clients\Cactus\Jobs\SearchCactusCategoryJob;
use App\Services\Sources\Clients\Enter\Jobs\SearchEnterCategoryJob;
use App\Services\Sources\Clients\Maximum\Jobs\SearchMaximumCategoryJob;
use App\Services\Sources\Clients\Ultra\Jobs\SearchUltraCategoryJob;
use App\Services\Sources\Orchestration\CatalogCategoryRunResult;
use App\Services\Sources\Orchestration\CatalogSource;
use App\Services\Sources\Orchestration\CatalogSourceRunResult;
use App\Services\Sources\Orchestration\CatalogSourcesOrchestrator;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    config()->set('logging.channels.sources.run', ['driver' => 'null']);
});

it('dispatches all catalog sources as one batch', function () {
    Bus::fake();

    $this->artisan('sources:catalog:run', ['--mode' => 'queue'])
        ->assertSuccessful();

    Bus::assertBatched(function (PendingBatch $batch) {
        return $batch->jobs->count() === 6
            && $batch->jobs->filter(fn ($job) => $job instanceof SearchEnterCategoryJob)->count() === 2
            && $batch->jobs->filter(fn ($job) => $job instanceof SearchCactusCategoryJob)->count() === 1
            && $batch->jobs->filter(fn ($job) => $job instanceof SearchMaximumCategoryJob)->count() === 1
            && $batch->jobs->filter(fn ($job) => $job instanceof SearchUltraCategoryJob)->count() === 2;
    });
});

it('can dispatch only selected sources', function () {
    Bus::fake();

    $this->artisan('sources:catalog:run', [
        'sources' => ['enter', 'ultra'],
        '--mode' => 'queue',
    ])->assertSuccessful();

    Bus::assertBatched(fn (PendingBatch $batch) => $batch->jobs->count() === 4);
});

it('rejects unknown sources and modes without dispatching a batch', function () {
    Bus::fake();

    $this->artisan('sources:catalog:run', [
        'sources' => ['marketplace999'],
        '--mode' => 'queue',
    ])->assertExitCode(2);

    $this->artisan('sources:catalog:run', [
        '--mode' => 'later',
    ])->assertExitCode(2);

    Bus::assertNothingBatched();
});

it('shows every category with its duration and error', function () {
    $tv = new CatalogCategoryRunResult(
        source: CatalogSource::ENTER,
        category: 'TV',
        durationMs: 18_400,
    );
    $fridge = new CatalogCategoryRunResult(
        source: CatalogSource::ENTER,
        category: 'FRIDGE',
        durationMs: 22_100,
        exception: new \RuntimeException('HTTP 503'),
    );

    $orchestrator = Mockery::mock(CatalogSourcesOrchestrator::class);
    $orchestrator->shouldReceive('runSync')
        ->once()
        ->andReturnUsing(function ($sources, $runId, $onSourceStart, $onCategoryFinished) use ($tv, $fridge) {
            $onSourceStart(CatalogSource::ENTER);
            $onCategoryFinished($tv);
            $onCategoryFinished($fridge);

            return [new CatalogSourceRunResult(
                source: CatalogSource::ENTER,
                durationMs: 40_500,
                exception: $fridge->exception,
                categories: [$tv, $fridge],
            )];
        });

    $this->app->instance(CatalogSourcesOrchestrator::class, $orchestrator);

    $this->artisan('sources:catalog:run', ['sources' => ['enter']])
        ->expectsOutputToContain('[1/1] Enter')
        ->expectsOutputToContain('TV               completed     18.4s')
        ->expectsOutputToContain('FRIDGE           failed        22.1s  HTTP 503')
        ->assertFailed();
});
