<?php

namespace App\Services\Listing\Clients;

use App\Data\EntityMasterData;
use App\Models\EntityMaster;
use App\Services\Listing\BaseListing;
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
        $query = EntityMaster::query();

        $query->with(['entities' => function ($query) {
            $query->orderBy('data->is_out_of_stock');
        }]);

        return $query;
    }

    /**
     * Retrieves a paginated resource collection of games and discounts.
     *
     * @return PaginatedDataCollection
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
     *
     * @return LengthAwarePaginator
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
            'sort' => $request->query('sort', ["release_date" => 'desc']),
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

        return $query->when(
            $min !== null && $max !== null,
            fn (Builder $query) => $query->whereHas(
                'entities',
                function (Builder $query) use ($min, $max) {
                    $query->where('data->price', '>=', intval($min));
                    $query->where('data->price', '<=', intval($max));
                }
            )->when(
                $this->getQueryParam('brands'),
                fn (Builder $query, $brands) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->whereIn('data->brand', $brands)
                )
            )->when(
                $this->getQueryParam('sources'),
                fn (Builder $query, $sources) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->whereIn('source', $sources)
                )
            )->when(
                $this->getQueryParam('has_discount'),
                fn (Builder $query) => $query->whereHas(
                    'entities',
                    fn (Builder $query) => $query->where('data->discount', '>', 0)
                )
            )
        );
    }

//    protected function applySearch(Builder $query): Builder
//    {
//        $searchTerm = $this->getQueryParam('search');
//
//        if (empty($searchTerm)) {
//            return $query;
//        }
//
//        return $query->where(
//            fn ($query) => $query->where('games.title', 'like', '%' . $searchTerm . '%')
//        );
//    }
}
