<?php

namespace App\Services\Sources\Clients\RabotaMd\Adapters;

use App\Services\Sources\Clients\RabotaMd\Data\JobData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use App\Services\Sources\Enums\SourceClientType;

class JobDataAdapter implements AdapterInterface
{
    public static function toGeneral(JobData $data, EntityFilter $filter): EntityData
    {
        return EntityData::from([
            "external_id" => $data->external_id,
            "title" => $data->title,
            "source" => SourceClientType::RABOTA_MD,
            "filter_type" => $filter,
            "data" => $data->toArray(),
        ]);
    }
}
