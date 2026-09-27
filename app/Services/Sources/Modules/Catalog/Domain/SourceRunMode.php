<?php

namespace App\Services\Sources\Modules\Catalog\Domain;

enum SourceRunMode: string
{
    case SYNC = 'sync';
    case QUEUE = 'queue';
}
