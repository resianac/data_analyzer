<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntityMaster extends Model
{
    protected $guarded = [];


    public function getRouteKeyName(): string
    {
        return 'match_id';
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class, 'entity_master_id');
    }
}
