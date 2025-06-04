<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\ApproveExamRequest;
use App\Http\Requests\Admin\RejectExamRequest;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function pending(Request $request): JsonResponse
    {
        $query = Exam::pending()->with(['subject.career.university', 'uploader']);

        // Filtrar por universidad si se especifica
        if ($request->has('university_id')) {
            $query->byUniversity($request->university_id);
        }

        // Filtrar por carrera si se especifica
        if ($request->has('career_id')) {
            $query->byCareer($request->career_id);
        }

        // Filtrar por materia si se especifica
        if ($request->has('subject_id')) {
            $query->bySubject($request->subject_id);
        }

        $exams = $query->latest()->paginate(10);

        return response()->json($exams);
    }

    public function approve(ApproveExamRequest $request, Exam $exam): JsonResponse
    {
        if (!$exam->canBeApprovedBy($request->user())) {
            return response()->json(['message' => 'No tienes permiso para aprobar este examen'], 403);
        }

        if ($exam->approve($request->user())) {
            return response()->json(['message' => 'Examen aprobado exitosamente']);
        }

        return response()->json(['message' => 'No se pudo aprobar el examen'], 500);
    }

    public function reject(RejectExamRequest $request, Exam $exam): JsonResponse
    {
        if (!$exam->canBeApprovedBy($request->user())) {
            return response()->json(['message' => 'No tienes permiso para rechazar este examen'], 403);
        }

        if ($exam->reject($request->user(), $request->rejection_reason)) {
            return response()->json(['message' => 'Examen rechazado exitosamente']);
        }

        return response()->json(['message' => 'No se pudo rechazar el examen'], 500);
    }
}
