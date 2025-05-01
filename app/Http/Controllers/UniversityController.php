<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Requests\UpdateUniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UniversityResourceCollection;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
            $universities->where('name', 'LIKE', '%' . $searchTerm . '%');
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
        $university = University::query()->create($request->validated());
        return new UniversityResource($university);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university): UniversityResource
    {
        $university->load('Careers');
        return new UniversityResource($university);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUniversityRequest $request, University $university): UniversityResource
    {
        $university->update($request->validated());
        return new UniversityResource($university);
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
