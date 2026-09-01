<?php

namespace App\Http\Controllers;

use App\Data\EntityData;
use App\Data\EntityMasterData;
use App\Models\Entity;
use App\Models\EntityMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $masters = EntityMaster::with(['entities' => function ($query) {
            $query->orderBy('data->is_out_of_stock');
        }])
            ->has('entities', '>', 1)
            ->paginate();

        return Inertia::render('entity/Index', [
             'masters' => EntityMasterData::collect(
                 $masters,
                 PaginatedDataCollection::class
             )
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
    public function show(string $id)
    {
        //
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
