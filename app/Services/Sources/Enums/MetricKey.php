<?php

namespace App\Services\Sources\Enums;

enum MetricKey: string
{
    case FLAT_AVG_PPM_1ROOM = 'flat_avg_pricePerMeter_1room';
    case FLAT_AVG_PPM_2ROOMS = 'flat_avg_pricePerMeter_2rooms';

    case FLAT_TOTAL_SOLD = 'flat_total_sold';
    case FLAT_AVG_SOLD_PRICE = 'flat_avg_sold_price';
    case FLAT_AVG_SOLD_PPM = 'flats_avg_sold_ppm';
    case FLAT_AVG_DAYS_STAYING = 'flat_avg_days_staying';
    case FLAT_TOP_SOLD_OWNER = 'flat_top_sold_owner';
    case FLAT_TOP_SOLD_TITLE = 'flat_top_sold_title';
}
