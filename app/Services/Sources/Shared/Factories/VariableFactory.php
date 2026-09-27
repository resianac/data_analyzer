<?php

namespace App\Services\Sources\Shared\Factories;

use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\MetricFilter;

class VariableFactory extends BaseFactory
{
    protected function getClassSuffix(): string
    {
        return 'Variables';
    }

    protected function getSubDirectory(EntityFilter|MetricFilter|null $filter = null): string
    {
        return 'Filters\\Variables';
    }

    protected function getExpectedInterface(): string
    {
        return '';
    }
}
