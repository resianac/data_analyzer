<?php

namespace App\Services\Listing\Clients;

use App\Data\EntityMasterData;
use App\Models\Entity;
use App\Models\EntityMaster;
use App\Services\Listing\BaseListing;
use App\Services\Listing\Enums\EntityMasterSort;
use App\Services\Listing\Traits\RequestQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\PaginatedDataCollection;

class EntityMasterListing extends BaseListing
{
    use RequestQuery;

    public function __construct(?EntityMaster $model = null)
    {
        parent::__construct($model);

        $this->initQueries();
    }

    public function getQuery(): Builder
    {
        return EntityMaster::query()
            ->has('entities')
            ->with('entities')
            ->orderBy(
                Entity::query()
                    ->select('data->is_out_of_stock')
                    ->whereColumn('entities.entity_master_id', 'entity_masters.id')
                    ->orderBy('data->is_out_of_stock')
                    ->limit(1)
            );
    }

    /**
     * Retrieves a paginated resource collection of games and discounts.
     */
    public function getPaginatedData(): PaginatedDataCollection
    {
        //        if (empty($this->queries['search'])) {
        //            return Cache::remember($this->generateCacheKey(), 3600 * 24, function () {
        //                return GameWithPlatformsResource::collection($this->processAndGet());
        //            });
        //        }

        return EntityMasterData::collect(
            $this->processAndGet(),
            PaginatedDataCollection::class
        );
    }

    /**
     * Retrieves a collection of games with related discounts and notifications.
     */
    protected function processAndGet(): LengthAwarePaginator
    {
        return $this->applyQuery($this->getQuery())
            ->paginate($this->getQueryParam('pageSize'));
    }

    /**
     * Initializes query parameters from the request.
     */
    protected function initQueries(): void
    {
        $request = $this->request;

        $this->queries = $this->queries->merge([
            'sort' => $request->query('sort', EntityMasterSort::SOURCE_COUNT->value),
            'pageSize' => $request->query('pageSize', 50),
            'price' => $request->query('price', [
                'min' => null,
                'max' => null,
            ]),
            'brands' => $request->filled('brands')
                ? explode(',', $request->query('brands'))
                : [],
            'sources' => $request->filled('sources')
                ? explode(',', $request->query('sources'))
                : [],
            'has_discount' => $request->boolean('has_discount', false),
        ]);
    }

    protected function applyFilters(Builder $query): Builder
    {
        $price = $this->getQueryParam('price', []);

        $min = $price['min'] ?? null;
        $max = $price['max'] ?? null;

        return $query
            ->when(
                $min !== null && $max !== null,
                fn (Builder $query) => $query->whereHas(
                    'entities',
                    function (Builder $query) use ($min, $max) {
                        $query->where('data->price', '>=', intval($min));
                        $query->where('data->price', '<=', intval($max));
                    }
                )
            )
            ->when(
                $this->getQueryParam('brands'),
                fn (Builder $query, $brands) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->whereIn('data->brand', $brands)
                )
            )
            ->when(
                $this->getQueryParam('sources'),
                fn (Builder $query, $sources) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->whereIn('source', $sources)
                )
            )
            ->when(
                $this->getQueryParam('has_discount'),
                fn (Builder $query) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->where('data->discount', '>', 0)
                )
            );
    }

    protected function applySearch(Builder $query): Builder
    {
        $searchTerm = $this->getQueryParam('search');

        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(
            fn ($query) => $query->where('title', 'like', '%'.$searchTerm.'%')
        );
    }

    protected function applySorts(Builder $query): Builder
    {
        $sort = EntityMasterSort::tryFrom( $this->getQueryParam('sort'));

        $query = match ($sort) {
            EntityMasterSort::DISCOUNT_DESC => $query
                ->addSelect([
                    'max_discount' => Entity::query()
                        ->selectRaw(
                            "MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.discount')) AS DECIMAL(10,2)))"
                        )
                        ->whereColumn('entity_master_id', 'entity_masters.id')
                ])
                ->orderByDesc('max_discount'),

            EntityMasterSort::PRICE_ASC => $query
                ->addSelect([
                    'sort_price' => Entity::query()
                        ->selectRaw(
                            "MIN(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.price')) AS DECIMAL(10,2)))"
                        )
                        ->whereColumn('entities.entity_master_id', 'entity_masters.id')
                ])
                ->orderBy('sort_price'),

            EntityMasterSort::PRICE_DESC => $query
                ->addSelect([
                    'sort_price' => Entity::query()
                        ->selectRaw(
                            "MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.price')) AS DECIMAL(10,2)))"
                        )
                        ->whereColumn('entities.entity_master_id', 'entity_masters.id')
                ])
                ->orderByDesc('sort_price'),

            /** Default: EntityMasterSort::SOURCE_COUNT */
            default => $query->orderBy(
                Entity::query()
                    ->selectRaw('COUNT(DISTINCT source)')
                    ->whereColumn('entities.entity_master_id', 'entity_masters.id'),
                'desc',
            ),
        };

        return $query->orderBy('entity_masters.id');
    }
}
