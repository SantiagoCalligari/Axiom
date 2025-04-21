<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Requests\UpdateUniversityRequest;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UniversityResourceCollection;
use App\Models\University;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): UniversityResourceCollection
    {
        $universities = University::query();
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
    public function destroy(University $university): Response
    {
        $university->delete();
        return response('Deleted successfully');
    }
}
