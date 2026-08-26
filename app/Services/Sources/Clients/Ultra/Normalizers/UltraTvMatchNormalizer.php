<?php

namespace App\Services\Sources\Clients\Ultra\Normalizers;

use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Support\BaseNormalizer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;
use Throwable;

class UltraTvMatchNormalizer extends BaseNormalizer
{
    protected array $stopWords = ['телевизор', 'TV', 'smart'];

    public function normalize(Data|UltraData $data): string|null
    {
        try {
            $cleanTitle = $this->cleanText(
                Str::of($data->title)->beforeLast(' ')
            );

            $diagonal = Str::of($cleanTitle)
                ->before(' ');

            $tech = Str::of($cleanTitle)
                ->after($diagonal)
                ->betweenFirst(' ', ' ');

            $brand = Str::of($cleanTitle)
                ->after($tech)
                ->betweenFirst(' ', ' ');

            $model = Str::of($cleanTitle)
                ->after($brand);

            if (
                $diagonal->value() === '' ||
                $tech->value() === '' ||
                $brand->value() === '' ||
                $model->value() === ''
            ) {
                Log::channel('sources.entity')->error(
                    "[NORMALIZER] Invalid title structure [{$data->title}]",
                );

                return null;
            }

            return Str::of("{$brand} {$model} {$diagonal} {$tech}")
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
