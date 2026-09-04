<?php

namespace App\Http\Controllers;

use App\Data\EntityData;
use App\Data\EntityMasterData;
use App\Models\Entity;
use App\Models\EntityMaster;
use App\Services\Listing\Clients\EntityMasterListing;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('entity/Index', [
            'masters' => (new EntityMasterListing())->getPaginatedData(),
            'brands' => Entity::query()
                ->whereHas('master', function ($query) {
                    $query->where('category', 'tv');
                })
                ->whereNotNull('data->brand')
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.brand')) as brand")
                ->selectRaw('COUNT(*) as count')
                ->groupByRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.brand'))")
                ->orderBy('brand')
                ->get()
                ->map(fn ($item) => [
                    'value' => $item->brand,
                    'label' => $item->brand,
                    'count' => $item->count,
                ])
                ->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityMaster $master)
    {
        $master->load(['entities' => function ($query) {
            $query->with('metrics')->orderBy('source');
        }]);

        return Inertia::render('entity/Show', [
            'master' => EntityMasterData::from($master),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
