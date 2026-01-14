<?php

namespace App\Console\Commands\Sources\Marketplace999\Metric;

use App\Jobs\SendMessageToTelegram;
use App\Models\Metric;
use App\Services\Repository\MetricRepository;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\CreateFlatSaleDynamicsMetricAction;
use App\Services\Sources\Data\MetricData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\FormatterFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FlatSaleDynamics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sources:marketplace999:metric:sale-dynamics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate flat sale dynamics metrics for previous week and save to metrics table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fromDate = now()->subWeek()->startOfWeek();
        $toDate = now()->subWeek()->endOfWeek();

        $this->info('Starting flat sale dynamics calculation...');

        $prevMetrics = (new MetricRepository())->getPreviousMetrics(
            SourceClientType::MARKETPLACE999,
            EntityFilter::FLAT_DEFAULT,
            [
                MetricKey::FLAT_TOTAL_SOLD,
                MetricKey::FLAT_AVG_SOLD_PRICE,
                MetricKey::FLAT_AVG_SOLD_PPM,
                MetricKey::FLAT_AVG_DAYS_STAYING,
                MetricKey::FLAT_TOP_SOLD_TITLE,
                MetricKey::FLAT_TOP_SOLD_OWNER,
            ]
        );
        $this->showValues("\nPrevious metrics:", $prevMetrics);

        $currentMetrics = (new CreateFlatSaleDynamicsMetricAction())->handle($fromDate, $toDate);
        $this->showValues("Current metrics:", $currentMetrics);

        Log::channel('sources.metric')->debug(
            "Flat sale dynamics calculation".
            json_encode([
                'previous' => $prevMetrics->pluck('value', 'key.value')->toArray(),
                'current'  => $currentMetrics->pluck('value', 'key.value')->toArray(),
            ], JSON_PRETTY_PRINT)
        );

        $formatter = (new FormatterFactory())->make(
            SourceClientType::MARKETPLACE999,
            MetricFilter::FLAT_SALE_DYNAMICS,
            [
                $currentMetrics, $prevMetrics,
                $fromDate, $toDate
            ]
        );

        $message = $formatter->get();

        SendMessageToTelegram::dispatch($message);
    }

    private function showValues(string $msg, Collection|array $metrics): void
    {
        $this->info($msg);

        /** @var MetricKey $metric */
        foreach ($metrics as $metric) {
            $this->warn(
                "{$metric->key->value}: ".
                (is_array($metric->value)
                    ? $metric->value['value']
                    : $metric->value
                )
            );
        }

        $this->newLine();
    }
}
