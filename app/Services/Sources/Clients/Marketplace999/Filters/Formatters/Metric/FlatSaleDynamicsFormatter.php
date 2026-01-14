<?php

namespace App\Services\Sources\Clients\Marketplace999\Filters\Formatters\Metric;

use App\Services\Sources\Enums\MetricKey;
use App\Services\Sources\Filters\BaseFormatter;
use Carbon\Carbon;
use stdClass;

class FlatSaleDynamicsFormatter extends BaseFormatter
{
    protected function processData(): static
    {
        [$currentMetrics, $previousMetrics, $fromDate, $toDate] = $this->subject;

        $curr = $this->normalizeMetrics($currentMetrics);
        $prev = $this->normalizeMetrics($previousMetrics);

        $this->data = new stdClass();

        $this->data->curr = $curr;
        $this->data->prev = $prev;
        $this->data->fromDate = $fromDate;
        $this->data->toDate = $toDate;

        return $this;
    }

    private function normalizeMetrics($metrics): array
    {
        $result = [];

        foreach ($metrics as $metric) {
            $key = $metric->key->value;

            if (is_array($metric->value) || is_object($metric->value)) {
                $result[$key] = (object) [
                    'value' => $metric->value['value'] ?? null,
                    'count' => $metric->value['count'] ?? null,
                ];
            } else {
                $result[$key] = (object) [
                    'value' => $metric->value,
                    'count' => null,
                ];
            }
        }

        return $result;
    }

    public function setHeader(...$params): static
    {
        $this->header = "📅 " .
            $this->data->fromDate->format('d.m.Y') .
            " — " .
            $this->data->toDate->format('d.m.Y');

        $this->header .= "\n🏠 *Динамика продаж квартир* (предыдущая неделя)";
        return $this;
    }

    public function setBody(...$params): static
    {
        $lines = [];

        $lines[] = $this->formatNumeric("📦 Продано квартир", MetricKey::FLAT_TOTAL_SOLD, " шт.");
        $lines[] = $this->formatNumeric("💰 Средняя цена", MetricKey::FLAT_AVG_SOLD_PRICE, " €");
        $lines[] = $this->formatNumeric("📊 €/м² проданных", MetricKey::FLAT_AVG_SOLD_PPM, " €/м²");
        $lines[] = $this->formatNumeric("⏱ Время экспозиции", MetricKey::FLAT_AVG_DAYS_STAYING," дней");
        $lines[] = $this->formatTopString("👤 Топ собственник", MetricKey::FLAT_TOP_SOLD_OWNER);
        $lines[] = $this->formatTopString("📄 Топ заголовок", MetricKey::FLAT_TOP_SOLD_TITLE);

        $this->body = implode("\n\n", array_filter($lines));

        return $this;
    }

    private function formatNumeric(string $label, MetricKey $key, string $unit = ''): string
    {
        $currVal = $this->data->curr[$key->value]->value ?? null;
        $prevVal = $this->data->prev[$key->value]->value ?? null;

        if (!$currVal && !$prevVal) {
            return '';
        }

        $currText = $currVal !== null ? number_format($currVal, 0, '', ' ') . $unit : '—';

        $change = '';
        if ($prevVal !== null && $currVal !== null && $prevVal != 0) {
            $diff = $currVal - $prevVal;
            $percent = round(($diff / $prevVal) * 100, 1);
            $sign = $diff > 0 ? '+' : ($diff < 0 ? '-' : '');
            $emoji = $diff > 0 ? '📈' : ($diff < 0 ? '📉' : '→');

            $absDiff = number_format(abs($diff), 0, '', ' ') . $unit;
            $change = "\n$emoji $sign$absDiff ($sign$percent%)";
        }

        return "*$label*: $currText$change";
    }

    private function formatTopString(string $label, MetricKey $key): string
    {
        $curr = $this->data->curr[$key->value] ?? null;
        $prev = $this->data->prev[$key->value] ?? null;

        if (!$curr->value && !$prev->value) {
            return 'ERROR';
        }

        $currText = $curr->value ?? '—';
        $countText = $curr?->count ? " ({$curr->count} шт.)" : '';

        $change = match (true) {
            is_null($prev) => '',
            $prev->value && $curr->value && $curr->value !== $prev->value => "\n*Ранее*: $prev->value",
            $prev->value && !$curr->value => "\n*Ранее*: $prev->value (сейчас нет данных)",
            !$prev->value && $curr->value => "\n*Ранее*: нет данных",
        };

        return "*$label*: \n*$currText* $countText $change";
    }
}
