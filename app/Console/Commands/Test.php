<?php

namespace App\Console\Commands;

use App\Services\Sources\Clients\Maximum\Enums\MaximumSearchParam;
use App\Services\Sources\Modules\Catalog\Infrastructure\Providers\Maximum\ImportMaximumCatalogCategory;
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
     *
     * @throws \Throwable
     */
    public function handle()
    {
        (new ImportMaximumCatalogCategory(MaximumSearchParam::TV))->handle();
    }
}
