<?php

namespace App\Services\Repository;

use App\Models\Entity;
use App\Models\EntityMaster;
use App\Services\Sources\Data\EntityData;
use App\Services\Sources\Data\EntityMasterData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class EntityMasterRepository
{
    public function __construct(protected EntityMasterData $masterData) {}

    public static function makeWithData(EntityMasterData $masterData): EntityMasterRepository
    {
        return new self($masterData);
    }

    public function resolveFor(string $matchId, string $title = ''): EntityMaster
    {
        $this->masterData->match_id = $matchId;
        $this->masterData->title = $title;

        return EntityMaster::firstOrCreate(
            [
                'match_id' => $this->masterData->match_id,
                'category' => $this->masterData->category,
            ],
            [
                'title' => $this->masterData->title,
            ]
        );
    }
}
