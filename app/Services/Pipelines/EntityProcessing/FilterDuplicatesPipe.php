<?php

namespace App\Services\Pipelines\EntityProcessing;

use App\Data\EntityData;
use App\Services\Repository\EntityRepository;
use Closure;
use Illuminate\Support\Collection;

/**
 * The Pipe checks if entities already exist in DB by specific fields
 */
class FilterDuplicatesPipe
{
    protected array $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public static function make(array $fields): static
    {
        return new static($fields);
    }

    /**
     * @param Collection<EntityData> $entities
     * @param Closure $next
     * @return mixed
     */
    public function handle(Collection $entities, Closure $next): mixed
    {
        $reallyNewEntities = $entities->filter(
            fn($entity) => (new EntityRepository())->filterNewEntity($entity, $this->fields)
        );

        return $next($reallyNewEntities);
    }
}
