<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectResourceCollection;
use App\Models\Career;
use App\Models\Subject;
use App\Models\University;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(University $university, Career $career): SubjectResourceCollection
    {
        $subject_query = Subject::query()->where('career_id', $career->id);
        return new SubjectResourceCollection($subject_query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request, University $university, Career $career): SubjectResource
    {
        $subject = $career->Subjects()->create($request->validated());
        return new SubjectResource($subject);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university, Career $career, Subject $subject): SubjectResource
    {
        $subject->load('administrators');
        return new SubjectResource($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(University $university, Career $career, UpdateSubjectRequest $request, Subject $subject): SubjectResource
    {
        $subject->update($request->validated());
        return new SubjectResource($subject);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university, Career $career, Subject $subject)
    {
        $subject->delete();
    }
}
