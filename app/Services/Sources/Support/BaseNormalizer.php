<?php

namespace App\Services\Sources\Support;

use App\Services\Sources\Contracts\NormalizerInterface;
use Illuminate\Support\Str;

abstract class BaseNormalizer implements NormalizerInterface
{
    protected array $stopWords = [];

    protected function cleanText(string $text): string
    {
        $text = Str::lower(Str::ascii($text));

        foreach ($this->stopWords as $stopWord) {
            $normalizedStopWord = Str::lower(Str::ascii($stopWord));
            $text = preg_replace('/\b' . preg_quote($normalizedStopWord, '/') . '\b/i', '', $text);
        }

        return Str::of($text)
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish();
    }

}
