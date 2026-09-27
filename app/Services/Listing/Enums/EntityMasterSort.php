<?php

namespace App\Services\Listing\Enums;

enum EntityMasterSort: string
{
    case SOURCE_COUNT = 'source_count';
    case DISCOUNT_DESC = 'discount_desc';
    case PRICE_ASC = 'price_asc';
    case PRICE_DESC = 'price_desc';
}
