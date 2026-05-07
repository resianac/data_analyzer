<?php

namespace App\Console\Commands\Sources;

use App\Services\Sources\Clients\Marketplace999\Actions\SearchFlatsAction;
use App\Services\Sources\Clients\RabotaMd\Actions\SearchJobsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sources:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command runs all available sources to fetch data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('▶ Starting sources run');

        try {
            $this->line('• Running jobs source...');
            (new SearchJobsAction())->handle();
            $this->info('✔ Jobs source finished successfully');

            $this->line('• Running flats source...');
            (new SearchFlatsAction())->handle();
            $this->info('✔ Flats source finished successfully');

            Log::channel('sources.command')->info('All sources finished successfully');
        } catch (\Throwable $e) {
            $this->error('✖ Sources run failed');

            Log::channel('sources.command')->error('[sources] Run error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        $this->info('■ Sources run completed');
    }
}
