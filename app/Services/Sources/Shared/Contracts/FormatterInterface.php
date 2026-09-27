<?php

namespace App\Services\Sources\Shared\Contracts;

interface FormatterInterface
{
    public static function make(mixed $subject): static;

    public function setHeader(...$params): static;

    public function setBody(...$params): static;

    public function get(): string;
}
