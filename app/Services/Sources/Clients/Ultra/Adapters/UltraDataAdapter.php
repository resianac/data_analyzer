<?php

namespace App\Services\Sources\Clients\Ultra\Adapters;

use App\Data\EntityData;
use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Data\SourceDataAttributes\ProductAttributes;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;

class UltraDataAdapter implements AdapterInterface
{
   public static function toGeneral(UltraData $data, EntityFilter $filter, string $matchId): EntityData
   {
       return EntityData::from([
           "external_id" => $data->external_id,
           "match_id" => $matchId,
           "title" => $data->title,
           "source" => SourceClientType::ULTRA,
           "filter_type" => $filter,
           "data" => (new ProductAttributes(
               price: $data->price,
               old_price: $data->old_price,
               discount: $data->discount ? (float) $data->discount : null,
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
