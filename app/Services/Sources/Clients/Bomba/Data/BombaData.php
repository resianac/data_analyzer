<?php

namespace App\Services\Sources\Clients\Bomba\Data;

use App\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;
use Spatie\LaravelData\Data;

class BombaData extends Data
{
    public function __construct(
        // TODO: entity fields
    ) {}

    public function toGeneral(EntityFilter $filter): EntityData
    {
        // TODO: map to general format EntityData
    }
}
