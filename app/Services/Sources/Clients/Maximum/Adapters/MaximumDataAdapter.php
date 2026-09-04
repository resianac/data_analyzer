<?php

namespace App\Services\Sources\Clients\Maximum\Adapters;

use App\Services\Sources\Clients\Maximum\Data\MaximumData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Data\EntityData;
use App\Services\Sources\Data\SourceDataAttributes\ProductAttributes;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;
use Illuminate\Support\Str;

class MaximumDataAdapter implements AdapterInterface
{
    public static function toGeneral(MaximumData $data, EntityFilter $filter, string $matchId): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "match_id" => $matchId,
            "title" => $data->title,
            "source" => SourceClientType::MAXIMUM,
            "filter_type" => $filter,
            "data" => (new ProductAttributes(
                price: $data->price,
                old_price: $data->old_price,
                discount: $data->discount ? (float) abs($data->discount) : 0,
                currency: 'MDL',
                brand: Str::of($matchId)->before('_')->value(),
                is_out_of_stock: $data->out_of_stock !== null,
                url: $data->url,
                image: $data->image,
                raw: ['variant' => $data->variant]
            ))->toArray(),
       ]);
    }
}
