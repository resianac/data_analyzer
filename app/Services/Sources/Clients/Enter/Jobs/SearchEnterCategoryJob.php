<?php

namespace App\Services\Sources\Clients\Enter\Jobs;

use App\Services\Sources\Clients\Enter\Actions\SearchEnterEntitiesAction;
use App\Services\Sources\Clients\Enter\Enums\EnterSearchParam;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SearchEnterCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly EnterSearchParam $category,
    ) {}

    public function handle(): void
    {
        (new SearchEnterEntitiesAction($this->category))->handle();
    }
}
