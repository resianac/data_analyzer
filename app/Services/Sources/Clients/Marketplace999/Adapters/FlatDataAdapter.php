<?php

namespace App\Services\Sources\Clients\Marketplace999\Adapters;

use App\Data\EntityData;
use App\Services\Sources\Clients\Marketplace999\Data\FlatData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;

class FlatDataAdapter implements AdapterInterface
{
    public static function toGeneral(FlatData $data, EntityFilter $filter): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "title" => $data->title,
            "source" => SourceClientType::MARKETPLACE999,
            "filter_type" => $filter,
            "data" => $data->toArray(),
            "external_last_update" => $data->reseted,
        ]);
    }
}
