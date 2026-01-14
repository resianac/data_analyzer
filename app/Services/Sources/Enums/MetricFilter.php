<?php

namespace App\Services\Sources\Enums;

enum MetricFilter: string
{
    case FLAT_AVERAGE_PPM = 'flat_average_ppm';
    case FLAT_SALE_DYNAMICS = 'flat_sale_dynamics';
}
