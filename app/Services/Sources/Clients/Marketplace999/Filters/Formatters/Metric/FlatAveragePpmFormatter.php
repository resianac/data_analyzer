<?php

namespace App\Services\Sources\Clients\Marketplace999\Filters\Formatters\Metric;

use App\Services\Sources\Filters\BaseFormatter;
use stdClass;

class FlatAveragePpmFormatter extends BaseFormatter
{
    protected function processData(): static
    {
        $metrics = $this->subject;

        $this->data = new stdClass();

        $this->data->currOneRoom = $metrics[0]->value ?? null;
        $this->data->currTwoRoom = $metrics[1]->value ?? null;

        $this->data->prevOneRoom = $metrics[2]->value ?? null;
        $this->data->prevTwoRoom = $metrics[3]->value ?? null;

        return $this;
    }

    public function setHeader(...$params): static
    {
        $this->header = "🏠 *Average Price Per Meter (PPM)*";
        return $this;
    }

    public function setBody(...$params): static
    {
        $oneRoomText = $this->formatRoom(
            "1-room flats",
            $this->data->currOneRoom,
            $this->data->prevOneRoom
        );

        $twoRoomText = $this->formatRoom(
            "2-room flats",
            $this->data->currTwoRoom,
            $this->data->prevTwoRoom
        );

        $this->body = "$oneRoomText\n$twoRoomText";

        return $this;
    }

    protected function formatRoom(string $label, ?float $current, ?float $previous): string
    {
        $currFormatted = $current !== null ? number_format($current, 0, '', ' ') . " €/m²" : 'N/A';

        $changeText = '';
        if ($previous !== null && $current !== null) {
            $diff = $current - $previous;
            $percent = $previous > 0 ? round((abs($diff) / $previous) * 100, 1) : 0;
            $sign = $diff >= 0 ? '+' : '-';
            $emoji = $diff >= 0 ? '📈' : '📉';

            $absDiff = number_format(abs($diff), 0, '', ' ') . " €/m²";

            $changeText = "\n$emoji (`$sign{$absDiff}`, $sign{$percent}%)\n";
        }

        return "*$label*: $currFormatted$changeText";
    }
}
