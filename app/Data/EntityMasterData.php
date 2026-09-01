<?php

namespace App\Data;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class EntityMasterData extends Data
{
    public function __construct(
        public int|Optional         $id,
        public string|null          $match_id,
        public string|null          $title,
        public string|null          $category,

        #[DataCollectionOf(EntityData::class)]
        public Collection|Optional  $entities,
        public Carbon|Optional|null $created_at,
        public Carbon|Optional|null $updated_at,
    ) {}
}
