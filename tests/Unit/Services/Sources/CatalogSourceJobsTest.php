<?php

use App\Services\Sources\Clients\Cactus\Actions\OrchestrateCactusSearchAction;
use App\Services\Sources\Clients\Cactus\Jobs\SearchCactusCategoryJob;
use App\Services\Sources\Clients\Enter\Actions\OrchestrateEnterSearchAction;
use App\Services\Sources\Clients\Enter\Jobs\SearchEnterCategoryJob;
use App\Services\Sources\Clients\Maximum\Actions\OrchestrateMaximumSearchAction;
use App\Services\Sources\Clients\Maximum\Jobs\SearchMaximumCategoryJob;
use App\Services\Sources\Clients\Ultra\Actions\OrchestrateUltraSearchAction;
use App\Services\Sources\Clients\Ultra\Jobs\SearchUltraCategoryJob;

it('builds one queued job for every configured catalog category', function () {
    $runId = 'test-run';

    $jobs = [
        ...OrchestrateEnterSearchAction::all()->jobs($runId),
        ...OrchestrateCactusSearchAction::all()->jobs($runId),
        ...OrchestrateMaximumSearchAction::all()->jobs($runId),
        ...OrchestrateUltraSearchAction::all()->jobs($runId),
    ];

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
