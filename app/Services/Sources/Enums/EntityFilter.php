<?php

namespace App\Services\Sources\Enums;

enum EntityFilter: string
{
    case FLAT_DEFAULT = 'flat_default';
    case ENTER_ENTITY = 'enter_entity';
    case ULTRA_ENTITY = 'ultra_entity';
    case BOMBA_ENTITY = 'bomba_entity';
    case CACTUS_ENTITY = 'cactus_entity';
    case MAXIMUM_ENTITY = 'maximum_entity';
}
