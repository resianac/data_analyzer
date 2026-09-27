<?php

namespace App\Services\Sources\Shared\Factories;

use App\Services\Sources\Shared\Enums\EntityFilter;
use App\Services\Sources\Shared\Enums\MetricFilter;
use App\Services\Sources\Shared\Enums\SourceClientType;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class BaseFactory
{
    protected string $baseNamespace = 'App\\Services\\Sources\\Clients';

    abstract protected function getClassSuffix(): string;

    abstract protected function getSubDirectory(EntityFilter|MetricFilter|null $filter = null): string;

    abstract protected function getExpectedInterface(): string;

    public function make(
        SourceClientType $sourceType,
        EntityFilter|MetricFilter $filter,
        ...$params
    ): object {
        $className = $this->buildClassName($sourceType, $filter);

        if (! class_exists($className)) {
            throw new InvalidArgumentException("Class {$className} does not exist.");
        }

        $instance = new $className(...$params);

        $this->validateInterface($instance);

        return $instance;
    }

    protected function buildClassName(
        SourceClientType $sourceType,
        EntityFilter|MetricFilter $filter,
    ): string {
        $sourceClassName = $this->formatSourceName($sourceType);
        $factoryClassName = $this->formatFactoryClassName($filter);
        $subDirPath = $this->getSubDirectory($filter);

        return sprintf(
            '%s\\%s\\%s\\%s',
            $this->baseNamespace,
            $sourceClassName,
            $subDirPath,
            $factoryClassName
        );
    }

    protected function formatSourceName(SourceClientType $type): string
    {
        return Str::of($type->value)
            ->studly()
            ->value();
    }

    protected function formatFactoryClassName(EntityFilter|MetricFilter $filter): string
    {
        return Str::of($filter->value)
            ->studly()
            ->append($this->getClassSuffix())
            ->value();
    }

    protected function validateInterface(object $instance): void
    {
        $expectedInterface = $this->getExpectedInterface();

        if ($expectedInterface !== '' && ! ($instance instanceof $expectedInterface)) {
            throw new InvalidArgumentException(
                "Class must implement {$expectedInterface}"
            );
        }
    }
}
