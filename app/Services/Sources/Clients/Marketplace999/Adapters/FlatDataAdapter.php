<?php

namespace App\Services\Sources\Clients\Marketplace999\Adapters;

use App\Services\Sources\Clients\Marketplace999\Data\FlatData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Enums\SourceEntityType;

class FlatDataAdapter implements AdapterInterface
{
    public static function toGeneral(FlatData $data): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "title" => $data->title,
            "source" => SourceClientType::MARKETPLACE999,
            "type" => SourceEntityType::FLAT,
            "data" => $data->toArray(),
            "external_last_update" => $data->reseted,
        ]);
    }
}
