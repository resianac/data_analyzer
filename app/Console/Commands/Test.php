<?php

namespace App\Console\Commands;

use App\Services\Sources\Clients\Marketplace999\Actions\SearchFlatsAction;
use App\Services\Sources\Clients\Marketplace999\Marketplace999Client;
use App\Services\Sources\Drivers\GraphQLDriver;
use Illuminate\Console\Command;

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
     */
    public function handle()
    {
        (new SearchFlatsAction())->handle();
    }
}
