<?php

namespace App\Services\Sources\Clients\Ultra\Adapters;

use App\Services\Sources\Clients\Ultra\Data\UltraData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Data\EntityData;
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
           "data" => $data->toArray(),
       ]);
   }
}
