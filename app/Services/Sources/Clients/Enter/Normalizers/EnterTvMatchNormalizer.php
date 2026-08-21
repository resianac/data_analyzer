<?php

namespace App\Services\Sources\Clients\Enter\Normalizers;

use App\Services\Sources\Clients\Enter\Data\EnterData;
use App\Services\Sources\Support\BaseNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Throwable;

class EnterTvMatchNormalizer extends BaseNormalizer
{
    protected array $stopWords = ['телевизор', 'TV'];

    public function normalize(Data|EnterData $data): string|null
    {
        try {
            $cleanTitle = $this->cleanText($data->title);

            $diagonal = Str::of($data->variant)
                ->before('/');

            $tech = Str::of($data->variant)
                ->betweenFirst('/', '/');

            return Str::of("{$cleanTitle} {$diagonal} {$tech}")
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
}
