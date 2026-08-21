<?php

namespace App\Casts;

use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class SchemalessAttributesCast extends SchemalessAttributes
{
    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string $key
     * @param  \Spatie\SchemalessAttributes\SchemalessAttributes $value
     * @param  array $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($this->isJsonArray($value)) {
            return $value;
        }

        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        if (! is_array(json_decode($json, true))) {
            return null;
        }

        return $json;
    }
}
