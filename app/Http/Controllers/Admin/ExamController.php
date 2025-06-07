<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\ApproveExamRequest;
use App\Http\Requests\Admin\RejectExamRequest;
use App\Models\Role;
use App\Http\Resources\ExamResource;

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
    public function show(Exam $exam): ExamResource|JsonResponse
    {
        $user = auth()->user();

        // Verificar permisos
        if (!$exam->canBeApprovedBy($user)) {
            return response()->json(['message' => 'No tienes permiso para ver este examen'], 403);
        }

        // Cargar las relaciones necesarias
        $exam->load(['subject.career.university', 'uploader']);

        return new ExamResource($exam);
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
        $user = $request->user();

        // Si es admin general, puede ver todos los exámenes
        if (!$user->hasRole(Role::ADMIN)) {
            // Si es admin de universidad, ver solo exámenes de su universidad
            if ($user->hasRole(Role::UNIVERSITY_ADMIN)) {
                $universityIds = $user->adminUniversities()->select('universities.id')->pluck('id');
                $query->whereHas('subject.career.university', function ($q) use ($universityIds) {
                    $q->whereIn('universities.id', $universityIds);
                });
            }
            // Si es admin de carrera, ver solo exámenes de su carrera
            elseif ($user->hasRole(Role::CAREER_ADMIN)) {
                $careerIds = $user->adminCareers()->select('careers.id')->pluck('id');
                $query->whereHas('subject.career', function ($q) use ($careerIds) {
                    $q->whereIn('careers.id', $careerIds);
                });
            }
            // Si es admin de materia, ver solo exámenes de su materia
            elseif ($user->hasRole(Role::SUBJECT_ADMIN)) {
                $subjectIds = $user->adminSubjects()->select('subjects.id')->pluck('id');
                $query->whereIn('subject_id', $subjectIds);
            }
        }

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
