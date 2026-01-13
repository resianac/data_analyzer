<?php

namespace App\Console\Commands\Sources\Marketplace999\Metric;

use App\Jobs\SendMessageToTelegram;
use App\Models\Metric;
use App\Services\Sources\Clients\Marketplace999\Actions\Metrics\PPM\CreateAvgPPMMetricAction;
use App\Services\Sources\Data\MetricData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\MetricFilter;
use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Filters\Factories\FormatterFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class FlatAveragePPM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sources:marketplace999:metric:ppm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command calculate average price per meter and set it to metrics table';

    /**
     * Execute the console command.
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->info('Starting average price per meter calculation...');

        try {
            $prevOneRoomMetric = Metric::getLatest(
                MetricKey::FLAT_AVG_PPM_1ROOM,
                SourceClientType::MARKETPLACE999,
                EntityFilter::FLAT_DEFAULT
            );

            $prevTwoRoomMetric = Metric::getLatest(
                MetricKey::FLAT_AVG_PPM_2ROOMS,
                SourceClientType::MARKETPLACE999,
                EntityFilter::FLAT_DEFAULT
            );

            /** @var Collection<MetricData> $metricCollect */
            [$currOneRoomMetric, $currTwoRoomMetric] = (new CreateAvgPPMMetricAction())->handle();

            $this->info("1 room average: {$currOneRoomMetric->value}");
            $this->info("2 rooms average: {$currTwoRoomMetric->value}");

            Log::channel('sources.metric')->debug(
                "Flat's average PPM calculation: " .
                json_encode([
                    'previous' => [
                        '1_room' => $prevOneRoomMetric?->value ?? "None",
                        '2_rooms' => $prevTwoRoomMetric?->value ?? "None",
                    ],
                    'current' => [
                        '1_room' => $currOneRoomMetric->value,
                        '2_rooms' => $currTwoRoomMetric->value,
                    ]
                ], JSON_PRETTY_PRINT)
            );

            $this->info("\nStarting formatting and sending message to Telegram...");

            $formatter = (new FormatterFactory())->make(
                SourceClientType::MARKETPLACE999,
                MetricFilter::FLAT_AVERAGE_PPM,
                [
                    $currOneRoomMetric, $currTwoRoomMetric,
                    $prevOneRoomMetric, $prevTwoRoomMetric,
                ]
            );

            $message = $formatter->get();

            SendMessageToTelegram::dispatch($message);

            $this->info('✅ Completed successfully!');
        } catch (Throwable $e) {
            Log::channel('sources.metric')->error("Error during flat's PPM metrics calculation", [
                'exception' => $e->getMessage(),
            ]);

            $this->error("❌ Error occurred: {$e->getMessage()}");

            throw $e;
        }
    }
}
