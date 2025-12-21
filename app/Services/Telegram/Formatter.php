<?php

namespace App\Services\Telegram;

class Formatter
{
    public static function makeMarkdown( $f): string
    {
        $price = number_format($f->data->price, 0, '', ' ') . ' €';
        $oldPrice = !empty($f->data->oldPrice)
            ? number_format($f->data->oldPrice, 0, '', ' ') . ' €'
            : null;
        $perMeter = $f->data->has("pricePerMeter")
            ? number_format((int)$f->data->pricePerMeter, 0, '', ' ') . ' €/м²'
            : null;

        $area = null;
        if ($f->data->price && $f->data->pricePerMeter) {
            $area = round($f->data->price / $f->data->pricePerMeter);
        }

        $discount = null;
        if ($oldPrice && $f->data->price < $f->data->oldPrice) {
            $discountPercent = round((1 - $f->data->price / $f->data->oldPrice) * 100);
            $discountAmount = number_format($f->data->oldPrice - $f->data->price, 0, '', ' ');
            $discount = "🔥 *Скидка {$discountPercent}%* (-{$discountAmount} €)";
        }

        $statusEmoji = $discount ? '🔥' : ($f->data->reseted ? '🔄' : '🆕');

        $title = mb_strlen($f->title) > 100
            ? mb_substr($f->title, 0, 100) . '…'
            : $f->title;
        $message = "$statusEmoji *{$title}*\n";

        $message .= "*{$area}м²* | *ID:* `{$f->external_id}`\n";
        $message .= str_repeat('─', 12) . "\n\n";

        $message .= "💰 *Цена:* {$price}\n";
        if ($perMeter) {
            $message .= "📐 *За м²:* {$perMeter}\n";
        }

        if (!empty($oldPrice)) {
            $message .= "📉 *Было:* {$oldPrice}\n";
        }

        if ($discount) {
            $message .= "{$discount}\n";
        }

        $message .= "\n";

        $specs = [];
        if ($f->data->has("rooms")) {
            $specs[] = "🏠 {$f->data->rooms}";
        }
        if ($f->data->has("floor") && $f->data->has("totalFloors")) {
            $specs[] = "*Этаж:* {$f->data->floor}/{$f->data->totalFloors}";
        }

        if (!empty($specs)) {
            $message .= implode('  •  ', $specs) . "\n\n";
        }

        $message .= "🔗 [Открыть объявление]({$f->data->url})\n";

        $timeText = $f->data->reseted ? "Обновлено" : "Добавлено";
        $message .= "🕒 {$timeText}: " . now()->format('d.m H:i');

        return $message;
    }
}
