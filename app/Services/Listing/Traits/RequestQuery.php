<?php

namespace App\Services\Listing\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use RuntimeException;

trait RequestQuery
{
    abstract protected function initQueries(): void;

    /**
     * Applies filter conditions to query (optional override)
     */
    protected function applyFilters(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Applies sorting to query (optional override)
     */
    protected function applySorts(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Applies sorting for specific field (optional override)
     *
     * @param string $field Field name to sort by
     * @param string $order Sorting direction (asc/desc)
     */
    protected function applySortField(Builder $query, string $field, string $order): void
    {
        // Default empty implementation
    }

    /**
     * Applies search conditions to query (optional override)
     */
    protected function applySearch(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Applies all available query modifications (filters, sorts, search)
     *
     * @return Builder Modified query builder
     */
    protected function applyQuery(Builder $query): Builder
    {
        return $query
            ->when(method_exists($this, 'applyFilters'), fn($q) => $this->applyFilters($q))
            ->when(method_exists($this, 'applySorts'), fn($q) => $this->applySorts($q))
            ->when(method_exists($this, 'applySearch'), fn($q) => $this->applySearch($q));
    }

    /**
     * Gets query parameter value with optional default
     *
     * @param string $key Parameter key
     * @param mixed|null $default Default value if key not found
     * @return mixed Parameter value or default
     */
    protected function getQueryParam(string $key, mixed $default = null): mixed
    {
        if (!isset($this->queries)) {
            throw new RuntimeException('Use initQueries() before using.');
        }

        return Arr::get($this->queries, $key, $default);
    }
}
