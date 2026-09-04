<?php

namespace App\Services\Sources\Clients\Maximum\Normalizers;

use App\Services\Sources\Clients\Maximum\Data\MaximumData;
use App\Services\Sources\Support\BaseNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Throwable;

class MaximumTvMatchNormalizer extends BaseNormalizer
{
    protected array $stopWords = [
        'телевизор',
        'tv',
        'smart',
        'qled',
        'miniled',
        'oled',
        'led',
        'nanocell',
    ];

    public function normalize(Data|MaximumData $data): string|null
    {
        try {
            $title = $this->cleanText(
                Str::of($data->title)
            );

            $diagonal = $this->extractDiagonal($title);

            if (!$diagonal) {
                Log::channel('sources.entity')->error(
                    "[NORMALIZER] Diagonal not found [{$data->title}]",
                );

                return null;
            }

            $title = preg_replace(
                '/(?<![\p{L}\d])' . preg_quote($diagonal, '/') . '(?![\p{L}\d])/u',
                '',
                $title,
                1
            );

            $title = $this->removeTechChar($title);

            return Str::of("{$title} {$diagonal}")
                ->replaceMatches('/[^a-zA-Zа-яА-Я0-9]+/u', ' ')
                ->squish()
                ->lower()
                ->replace(' ', '_')
                ->value();

        } catch (Throwable $e) {
            Log::channel('sources.entity')->error(
                "[NORMALIZER] Failed to normalize id in title: [{$data->title}] | {$e->getMessage()}",
                ['exception' => $e]
            );

            return null;
        }
    }

    private function extractDiagonal(string $title): ?string
    {
        preg_match_all(
            '/(?<!\d)(\d{2,3})(?!\d)(?!\s*Hz)/iu',
            $title,
            $matches
        );

        foreach ($matches[1] as $value) {
            $diagonal = (int) $value;

            if ($diagonal >= 19 && $diagonal <= 120) {
                return $value;
            }
        }

        return null;
    }

    private function removeTechChar(string $title): string
    {
        $title = preg_replace('/\b\d{2,4}\s*Hz\b/iu', '', $title);

        return preg_replace(
            '/\b(?:2K|4K|8K|Full\s*HD)\b/iu',
            '',
            $title
        );
    }
}
