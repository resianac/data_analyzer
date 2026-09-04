<?php

namespace App\Models;

use App\Casts\SchemalessAttributesCast;
use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Enums\EntityFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    protected $guarded = [];

    protected $casts = [
        "source" => SourceClientType::class,
        "filter_type" => EntityFilter::class,
        "data" => SchemalessAttributesCast::class,
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(EntityMaster::class, 'entity_master_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class);
    }
}
