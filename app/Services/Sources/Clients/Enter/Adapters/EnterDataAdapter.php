<?php

namespace App\Services\Sources\Clients\Enter\Adapters;

use App\Services\Sources\Clients\Enter\Data\EnterData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;

class EnterDataAdapter implements AdapterInterface
{
    public static function toGeneral(EnterData $data, EntityFilter $filter, string $matchId): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "match_id" => $matchId,
            "title" => $data->title,
            "source" => SourceClientType::ENTER,
            "filter_type" => $filter,
            "data" => $data->toArray(),
        ]);
    }
}
