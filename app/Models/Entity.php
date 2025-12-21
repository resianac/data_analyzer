<?php

namespace App\Models;

use App\Services\Sources\Enums\SourceClientType;
use App\Services\Sources\Enums\SourceEntityType;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Entity extends Model
{
    protected $guarded = [];

    protected $casts = [
        "source" => SourceClientType::class,
        "type" => SourceEntityType::class,
        "data" => SchemalessAttributes::class,
    ];
}
