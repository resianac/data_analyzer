<?php

namespace App\Services\Sources\Enums;

enum EntityFilter: string
{
    case FLAT_DEFAULT = 'flat_default';
    case CAR = 'car';
    case JOB = 'job';
}
