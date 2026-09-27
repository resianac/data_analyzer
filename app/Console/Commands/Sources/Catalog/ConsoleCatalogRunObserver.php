<?php

namespace App\Console\Commands\Sources\Catalog;

use App\Services\Sources\Modules\Catalog\Contracts\CatalogRunObserver;
use App\Services\Sources\Modules\Catalog\Domain\CatalogSource;
use App\Services\Sources\Modules\Catalog\Domain\Results\CategoryRunResult;
use Illuminate\Console\Command;

readonly class ConsoleCatalogRunObserver implements CatalogRunObserver
{
    public function __construct(private Command $command) {}

    public function sourceStarted(CatalogSource $source, int $position, int $total): void
    {
        $this->command->newLine();
        $this->command->line(sprintf('[%d/%d] %s', $position, $total, $source->label()));
    }

    public function categoryFinished(CategoryRunResult $result): void
    {
        $symbol = $result->succeeded() ? '<fg=green>✓</>' : '<fg=red>✗</>';
        $status = $result->succeeded() ? 'completed' : 'failed';
        $details = $result->exception === null ? '' : '  '.$result->exception->getMessage();

        $this->command->line(sprintf(
            '  %s %-16s %-10s %8s%s',
            $symbol,
            $result->category,
            $status,
            number_format($result->durationMs / 1000, 1).'s',
            $details,
        ));
    }
}
