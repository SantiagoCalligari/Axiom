<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\University;
use App\Models\Career;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;


class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ExamResource
    {
        $exam_query = Exam::query();
        return new ExamResource($exam_query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(University $university, Career $career, Subject $subject, StoreExamRequest $request): ExamResource
    {
        $file = $request->file('file');
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $filepath = $file->storePubliclyAs('exams', $fileName, 'public');
        $exam = Exam::query()->create([
            'user_id' => $request->user()->id,
            'subject_id' => $subject->id,
            'title' => $request->title,
            'professor_name' => $request->professor_name,
            'semester' => $request->semester,
            'year' => $request->year,
            'is_resolved' => $request->is_resolved ?? false, // Default to false if not provided
            'exam_type' => $request->exam_type,
            'exam_date' => $request->exam_date,
            'file_path' => $filepath,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
        return new ExamResource($exam);
    }

    public function show(University $university, Career $career, Subject $subject, Exam $exam): ExamResource
    {
        $exam->load('uploader');
        return new ExamResource($exam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(University $university, Career $career, Subject $subject, UpdateExamRequest $request, Exam $exam): ExamResource|JsonResponse // Updated return type hint
    {
        $validatedData = $request->validated();

        // Check if a new file is being uploaded
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');

            // 1. Delete the old file if it exists
            if ($exam->file_path && Storage::disk('public')->exists($exam->file_path)) {
                Storage::disk('public')->delete($exam->file_path);
            }

            // 2. Store the new file
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filepath = $file->storePubliclyAs('exams', $fileName, 'public');

            // 3. Update the validated data with new file information
            $validatedData['file_path'] = $filepath;
            $validatedData['original_file_name'] = $file->getClientOriginalName();
            $validatedData['mime_type'] = $file->getMimeType();
            $validatedData['file_size'] = $file->getSize();
        }

        // Update the exam model with the (potentially updated) validated data
        $exam->update($validatedData);

        return new ExamResource($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university, Career $career, Subject $subject, Exam $exam): JsonResponse
    {
        $user = Auth::user();
        // Using strict comparison and early return for clarity
        if ($user->id !== $exam->user_id and !request()->user()->hasRole(Role::ADMIN)) {
            throw new AuthorizationException('You are not authorized to delete this exam.');
        }

        $filepath = $exam->file_path;

        // Delete the exam record from the database
        $exam->delete();

        // Delete the file from storage if it exists
        if ($filepath && Storage::disk('public')->exists($filepath)) {
            Storage::disk('public')->delete($filepath);
        }

        return response()->json(['message' => 'Exam deleted successfully.'], 200);
    }
}
