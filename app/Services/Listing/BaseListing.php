<?php

namespace App\Services\Listing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class BaseListing
{
    protected ?Model $model;
    protected Request $request;
    protected Collection $queries;

    public function __construct(?Model $model = null)
    {
        $this->model = $model;
        $this->request = request();

        $this->defaultQueries();
    }

    abstract protected function initQueries(): void;

    /**
     * @param Model $model
     * @return static
     */
    public static function make(Model $model): static
    {
        return new static($model);
    }

    /**
     * @param Request $request
     * @return static
     */
    public static function makeWithRequest(Request $request): static
    {
        return (new static())->withRequest($request);
    }

    public function withRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    protected function defaultQueries(): void
    {
        $this->queries = collect([
            'page' => $this->request->query('page', 1),
            'pageSize' => $this->request->query('pageSize', 25),
            'search' => $this->request->query('search', ''),
            'sort' => $this->request->query('sort', []),
        ]);
    }

    /**
     * Generates a unique cache key based on query parameters excluding search.
     *
     * @return string
     */
    public function generateCacheKey(): string
    {
        return $this->request->query->count() > 0
            ? static::class . '_' . md5(serialize($this->queries->except('search')->toArray()))
            : static::class . '_native';
    }
}
