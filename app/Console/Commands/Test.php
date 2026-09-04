<?php

namespace App\Console\Commands;

use App\Services\Sources\Clients\Bomba\Actions\OrchestrateBombaSearchAction;
use App\Services\Sources\Clients\Bomba\Enums\BombaSearchParam;
use App\Services\Sources\Clients\Cactus\Actions\OrchestrateCactusSearchAction;
use App\Services\Sources\Clients\Cactus\Enums\CactusSearchParam;
use App\Services\Sources\Clients\Enter\Actions\OrchestrateEnterSearchAction;
use App\Services\Sources\Clients\Enter\Actions\SearchEnterEntitiesAction;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\CreateFlatSaleDynamicsMetricAction;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Generators\GetFlatSaleDynamicsGeneratorAction;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\Values\GetMostFrequentValueAction;
use App\Services\Sources\Clients\Maximum\Actions\OrchestrateMaximumSearchAction;
use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Clients\RabotaMd\Actions\SearchJobsAction;
use App\Services\Sources\Clients\Ultra\Actions\OrchestrateUltraSearchAction;
use App\Services\Sources\Clients\Ultra\Enums\UltraSearchParam;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
//        OrchestrateCactusSearchAction::only(CactusSearchParam::TV)->dispatchSync();
        OrchestrateMaximumSearchAction::only(MaximumSearchParam::TV)->dispatchSync();
//        OrchestrateEnterSearchAction::only(EnterSearchParam::TV)->dispatchSync();
//        OrchestrateUltraSearchAction::only(UltraSearchParam::TV)->dispatchSync();
    }
}
