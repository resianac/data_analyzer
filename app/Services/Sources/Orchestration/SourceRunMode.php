<?php

namespace App\Services\Sources\Orchestration;

enum SourceRunMode: string
{
    case SYNC = 'sync';
    case QUEUE = 'queue';
}
