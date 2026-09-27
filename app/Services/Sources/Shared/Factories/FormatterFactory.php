<?php

namespace App\Services\Sources\Shared\Factories;

use App\Services\Sources\Shared\Contracts\FormatterInterface;
use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\MetricFilter;
use InvalidArgumentException;

class FormatterFactory extends BaseFactory
{
    protected function getClassSuffix(): string
    {
        return 'Formatter';
    }

    protected function getSubDirectory(EntityFilter|MetricFilter|null $filter = null): string
    {
        if (is_null($filter)) {
            throw new InvalidArgumentException('Filter needs to be defined');
        }

        return 'Filters\\Formatters\\'.(
            $filter instanceof MetricFilter
                ? 'Metric'
                : 'Entity'
        );
    }

    protected function getExpectedInterface(): string
    {
        return FormatterInterface::class;
    }
}
