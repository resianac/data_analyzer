<?php

namespace App\Services\Sources\Clients\Cactus\Normalizers;

use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Support\BaseNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Throwable;

class CactusTvMatchNormalizer extends BaseNormalizer
{
    protected array $stopWords = ['телевизор', 'TV', 'smart'];

    public function normalize(Data|UltraData $data): string|null
    {
        try {
            $cleanTitle = $this->cleanText(
                Str::of($data->title)
            );

            $diagonal = $this->extractDiagonal($cleanTitle);

            if (!$diagonal) {
                return null;
            }

            $title = Str::of($cleanTitle)->replace(" $diagonal ", ' ')->value();
            $title = Str::of($title)->replace(" pro ", ' ')->value();

            return Str::of("{$title} {$diagonal}")
                ->replaceMatches('/[^a-zA-Zа-яА-Я0-9]+/u', ' ')
                ->squish()
                ->lower()
                ->replace(' ', '_');

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
            '/(?<!\d)(\d{2,3})(?!\d)/u',
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
}
