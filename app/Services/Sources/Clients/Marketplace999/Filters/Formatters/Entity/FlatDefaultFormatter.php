<?php

namespace App\Services\Sources\Clients\Marketplace999\Filters\Formatters\Entity;

use App\Services\Sources\Filters\BaseFormatter;
use stdClass;

class FlatDefaultFormatter extends BaseFormatter
{
    protected array $watch = [
        'price', 'pricePerMeter'
    ];

    protected function processData(): static
    {
        $entityData = $this->subject->data;

        $this->data = new stdClass();
        $this->data->price = '0 €';
        $this->data->oldPrice = null;
        $this->data->pricePerMeter = null;
        $this->data->area = null;
        $this->data->discountPercent = null;
        $this->data->discountAmount = null;
        $this->data->timeText = $this->getWatchedChanges() ? "🔥 ИЗМЕНЕНА ЦЕНА" : "🆕 *Добавлено*";

        $this->data->price = $this->number($entityData->price, ' €');
        $this->data->oldPrice = $this->number($entityData->oldPrice, ' €');
        $this->data->pricePerMeter = $this->number($entityData->pricePerMeter, ' €/м²');
        $this->data->area = $entityData->price && $entityData->pricePerMeter
            ? round($entityData->price / $entityData->pricePerMeter)
            : null;
        if ($this->data->oldPrice && $this->data->price < $this->data->oldPrice) {
            $this->data->discountPercent = round((1 - $entityData->price / $entityData->oldPrice) * 100);
            $this->data->discountAmount = $this->number($entityData->oldPrice - $entityData->price);
        }
        if (!$this->getWatchedChanges()) {
            $this->data->timeText = $entityData->reseted ? "🔄 *Обновлено*" : "🆕 *Новое*";
        }

        return $this;
    }

    public function setHeader(...$params): static
    {
        $statusEmoji = $this->data->discountPercent
            ? "🔥 {$this->data->timeText}"
            : $this->data->timeText;

        $title = mb_strlen($this->subject->title) > 100
            ? mb_substr($this->subject->title, 0, 100) . '…'
            : $this->subject->title;

        $source = mb_strtoupper($this->subject->source->value);

        $this->header = "*{$source}*";
        $this->header .= " • {$this->subject->filter_type->value}\n";

        $this->header .= "$statusEmoji\n*{$title}*\n";
        $this->header .= $this->addIf($this->data->area, "*%s* м² | ") . "*ID:* `{$this->subject->external_id}`";

        return $this;
    }

    public function setBody(...$params): static
    {
        $this->body = $this->changedField(
            "price",
            $this->data->price,
            "💰 *Цена:*"
        );
        $this->body .= $this->changedField(
            "pricePerMeter",
            $this->data->pricePerMeter,
            "\n📐 *За м²:*"
        );

        $this->body .= $this->addIf($this->data->oldPrice, "\n📉 *Было:* %s");
        $this->body .= $this->addIf(
            [$this->data->discountPercent, $this->data->discountAmount],
            "\n🔥 *Скидка %s%%* (-%s €)"
        );

        $this->body .= "\n";

        $this->body .= $this->addIf($this->subject->data->rooms, "\n🏠 %s");
        $this->body .= $this->addIf(
            [$this->subject->data->floor, $this->subject->data->totalFloors],
            "  •  *Этаж:* %s/%s"
        );

        $this->body .= "\n\n🔗 [Открыть объявление]({$this->subject->data->url})";
        $this->body .= "\n{$this->data->timeText}: " . now()->format('d.m H:i');

        return $this;
    }
}
