<?php

namespace App\Services\Sources\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EntityMasterData extends Data
{
    public function __construct(
        public int|Optional         $id,
        public string|null          $match_id,
        public string|null          $title,
        public string|null          $category,
        public Carbon|Optional|null $created_at,
        public Carbon|Optional|null $updated_at,
    ) {}
}
