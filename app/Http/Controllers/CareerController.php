<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCareerRequest;
use App\Http\Requests\UpdateCareerRequest;
use App\Http\Resources\CareerResource;
use App\Http\Resources\CareerResourceCollection;
use App\Models\Career;
use App\Models\University;
use Illuminate\Support\Facades\DB;

class CareerController extends Controller
{
    public function index(University $university): CareerResourceCollection
    {
        $career_query = Career::query()->where('university_id', $university->id);
        return new CareerResourceCollection($career_query->get());
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCareerRequest $request, University $university): CareerResource
    {
        $career = $university->Careers()->create($request->validated());
        return new CareerResource($career);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university, Career $career): CareerResource
    {
        $career->load('Subjects');
        return new CareerResource($career);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(University $university, UpdateCareerRequest $request, Career $career): CareerResource
    {
        $career->update($request->validated());
        return new CareerResource($career);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university, Career $career)
    {
        $career->delete();
        return response('Deleted successfully');
    }
}
