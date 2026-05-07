<?php

namespace App\Services\Sources\Clients\RabotaMd\Filters\Formatters\Entity;

use App\Services\Sources\Filters\BaseFormatter;
use stdClass;

class JobFormatter extends BaseFormatter
{
    protected array $watch = [
        'salary',
    ];

    protected function processData(): static
    {
        $entityData = $this->subject->data;

        $this->data = new stdClass();

        $this->data->salary = $entityData->salary;
        $this->data->company = $entityData->company;
        $this->data->city = $entityData->city;
        $this->data->externalTitle = $this->subject->external_title;
        $this->data->externalUrl = $entityData->external_url;

        $this->data->timeText = $this->getWatchedChanges()
            ? '💰 *ИЗМЕНЕНА ЗАРПЛАТА*'
            : '';

        return $this;
    }

    public function setHeader(...$params): static
    {
        $title = mb_strlen($this->subject->title) > 100
            ? mb_substr($this->subject->title, 0, 100) . '…'
            : $this->subject->title;

        $source = mb_strtoupper($this->subject->source->value);
        $filter = $this->subject->filter_type->value;

        $this->header = "*{$source}*";
        $this->header .= " • $filter\n";

        $this->header .= "{$this->data->timeText}\n";
        $this->header .= "*{$title}*\n";
        $this->header .= "*ID:* `{$this->subject->external_id}`";

        return $this;
    }

    public function setBody(...$params): static
    {
        $this->body = $this->changedField(
            'salary',
            $this->data->salary,
            '💵 *Зарплата:*'
        );

        $this->body .= $this->addIf(
            $this->data->salary,
            "\n"
        );

        $this->body .= $this->addIf(
            $this->data->company,
            "🏢 *Компания:* %s"
        );

        $this->body .= $this->addIf(
            $this->data->city,
            "\n📍 *Город:* %s"
        );

        $this->body .= $this->addIf(
            $this->data->externalTitle,
            "\n📝 *Оригинальный заголовок:* %s"
        );

        $this->body .= $this->addIf(
            $this->data->externalUrl,
            "\n🌐 *External URL:* %s"
        );

        $this->body .= "\n\n🔗 [Открыть вакансию]({$this->subject->data->url})";

        return $this;
    }
}
