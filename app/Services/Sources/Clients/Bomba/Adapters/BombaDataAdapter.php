<?php

namespace App\Services\Sources\Clients\Bomba\Adapters;

use App\Services\Sources\Clients\Bomba\Data\BombaData;
use App\Services\Sources\Contracts\AdapterInterface;
use App\Data\EntityData;
use App\Services\Sources\Enums\EntityFilter;

class BombaDataAdapter implements AdapterInterface
{
   public static function toGeneral(BombaData $data, EntityFilter $filter): EntityData
   {
       return EntityData::from([

       ]);
   }
}
