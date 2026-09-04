<?php

namespace App\Services\Sources\Clients\Cactus\Adapters;

use App\Services\Sources\Clients\Cactus\Data\CactusData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Data\EntityData;
use App\Services\Sources\Data\SourceDataAttributes\ProductAttributes;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;

class CactusDataAdapter implements AdapterInterface
{
    public static function toGeneral(CactusData $data, EntityFilter $filter, string $matchId): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "match_id" => $matchId,
            "title" => $data->title,
            "source" => SourceClientType::CACTUS,
            "filter_type" => $filter,
            "data" => (new ProductAttributes(
                price: $data->price,
                old_price: $data->old_price,
                discount: $data->old_price && $data->price
                    ? round(
                        (float) abs(
                            (($data->old_price - $data->price) / $data->old_price) * 100
                        ),
                        1
                    )
                    : 0,
                currency: 'MDL',
                brand: null,
                is_out_of_stock: $data->out_of_stock !== null,
                url: $data->url,
                image: $data->image,
                raw: ['variant' => $data->variant]
            ))->toArray(),
        ]);
    }
}
