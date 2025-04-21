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
    /**
     * Display a listing of the resource.
     */
    public function index(University $university): CareerResourceCollection {}

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
    public function show(Career $career)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCareerRequest $request, Career $career)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Career $career)
    {
        //
    }
}
