<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Requests\UpdateUniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UniversityResourceCollection;
use App\Models\University;
use App\Models\UniversityAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): UniversityResourceCollection
    {
        $universities = University::query();

        if ($request->has('search') && !empty($request->query('search'))) {
            $searchTerm = $request->query('search');
            $universities = University::fuzzySearch($searchTerm);
            return new UniversityResourceCollection($universities);
        }

        $limit = $request->query('limit', 5);
        $universities->limit($limit);

        return new UniversityResourceCollection($universities->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUniversityRequest $request): UniversityResource
    {
        return DB::transaction(function () use ($request) {
            $university = University::create($request->only(['name', 'description']));
            
            if ($request->has('aliases')) {
                foreach ($request->aliases as $alias) {
                    $university->aliases()->create(['alias' => $alias]);
                }
            }

            return new UniversityResource($university->load('aliases'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university): UniversityResource
    {
        $university->load(['Careers', 'administrators']);
        return new UniversityResource($university);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUniversityRequest $request, University $university): UniversityResource
    {
        return DB::transaction(function () use ($request, $university) {
            $university->update($request->only(['name', 'description']));
            
            if ($request->has('aliases')) {
                // Eliminar alias existentes
                $university->aliases()->delete();
                
                // Crear nuevos alias
                foreach ($request->aliases as $alias) {
                    $university->aliases()->create(['alias' => $alias]);
                }
            }

            return new UniversityResource($university->load('aliases'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        $university->delete();
        return response('Deleted successfully');
    }
}
