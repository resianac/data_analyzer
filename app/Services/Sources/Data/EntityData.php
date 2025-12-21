<?php

namespace App\Services\Sources\Data;

use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Enums\SourceEntityType;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EntityData extends Data
{
    public function __construct(
        public int|Optional $id,
        public string $external_id,
        public string $title,
        public SourceClientType $source,
        public SourceEntityType $type,
        public array $data,
        public Carbon|Optional|null $external_last_update,
        public Carbon|Optional|null $created_at,
        public Carbon|Optional|null $updated_at,
    ) {}
}
