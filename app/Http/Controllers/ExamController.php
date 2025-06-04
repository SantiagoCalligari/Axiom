<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ExamResourceCollection; // Usar Collection Resource
use App\Models\Exam;
use App\Models\Subject;
use App\Models\University;
use App\Models\Career;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request; // Importar Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;


class ExamController extends Controller
{
    /**
     * Display a listing of the resource for a specific subject.
     * Added filtering and sorting.
     */
    // Añadir parámetros de ruta e inyectar Request
    public function index(University $university, Career $career, Subject $subject, Request $request): ExamResourceCollection
    {
        $exam_query = Exam::query()
            ->where('subject_id', $subject->id)
            ->where('approval_status', 'approved'); // Solo mostrar exámenes aprobados

        if ($request->filled('professor')) {
            $exam_query->where('professor_name', 'LIKE', '%' . $request->query('professor') . '%');
        }

        if ($request->filled('semester')) {
            $exam_query->where('semester', 'LIKE', '%' . $request->query('semester') . '%');
        }

        if ($request->has('is_resolved')) {
            $isResolved = filter_var($request->query('is_resolved'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isResolved !== null) {
                $exam_query->where('is_resolved', $isResolved);
            }
        }

        $sortBy = $request->query('sort_by', 'exam_date'); // Default: exam_date
        $sortOrder = $request->query('sort_order', 'desc'); // Default: desc (más recientes primero)

        $allowedSortColumns = ['exam_date', 'title', 'professor_name', 'year', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'exam_date'; // Reset a default si no es válido
        }
        // Validar orden
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Reset a default si no es válido
        }

        $exam_query->orderBy($sortBy, $sortOrder);

        $perPage = $request->query('per_page', 15); // Default 15 por página
        $exams = $exam_query->paginate($perPage);
        return new ExamResourceCollection($exams);

        // --- Sin Paginación (como estaba antes) ---
        //$exams = $exam_query->get();
        //return new ExamResourceCollection($exams); // Usar ExamResourceCollection
    }

    public function store(University $university, Career $career, Subject $subject, StoreExamRequest $request): ExamResource
    {
        $file = $request->file('file');
        // Usar slug para nombre de archivo más limpio
        $baseName = Str::slug($request->title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileName = $baseName . '-' . time() . '.' . $file->getClientOriginalExtension();

        $filepath = $file->storePubliclyAs('exams', $fileName, 'public');
        $exam = Exam::query()->create([
            'user_id' => $request->user()->id,
            'subject_id' => $subject->id,
            'title' => $request->title,
            'professor_name' => $request->professor_name,
            'semester' => $request->semester,
            'year' => $request->year,
            'is_resolved' => $request->boolean('is_resolved'), // Usar boolean()
            'exam_type' => $request->exam_type,
            'exam_date' => $request->exam_date,
            'file_path' => $filepath,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            // 'slug' => Str::slug($request->title . '-' . time()) // Generar slug si lo necesitas
        ]);
        return new ExamResource($exam);
    }

    public function show(University $university, Career $career, Subject $subject, Exam $exam): ExamResource|JsonResponse
    {
        // Verificar que el examen pertenece a la materia, carrera y universidad correctas
        if ($exam->subject_id !== $subject->id || $subject->career_id !== $career->id || $career->university_id !== $university->id) {
            return response()->json(['message' => 'Examen no encontrado'], 404);
        }

        // Permitir ver el examen si está aprobado o si el usuario es el subidor
        if ($exam->approval_status !== 'approved' && $exam->user_id !== auth()->id()) {
            return response()->json(['message' => 'No tienes permiso para ver este examen'], 403);
        }

        $exam->load('uploader');
        return new ExamResource($exam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(University $university, Career $career, Subject $subject, UpdateExamRequest $request, Exam $exam): ExamResource|JsonResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            if ($exam->file_path && Storage::disk('public')->exists($exam->file_path)) {
                Storage::disk('public')->delete($exam->file_path);
            }
            $baseName = Str::slug($validatedData['title'] ?? $exam->title);
            $fileName = $baseName . '-' . time() . '.' . $file->getClientOriginalExtension();
            $filepath = $file->storePubliclyAs('exams', $fileName, 'public');

            $validatedData['file_path'] = $filepath;
            $validatedData['original_file_name'] = $file->getClientOriginalName();
            $validatedData['mime_type'] = $file->getMimeType();
            $validatedData['file_size'] = $file->getSize();
        }

        // Asegurar que is_resolved se maneje correctamente si viene en el request
        if ($request->has('is_resolved')) {
            $validatedData['is_resolved'] = $request->boolean('is_resolved');
        }

        $exam->update($validatedData);
        return new ExamResource($exam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university, Career $career, Subject $subject, Exam $exam): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $exam->user_id and !$user->hasRole(Role::ADMIN)) { // Simplificado
            throw new AuthorizationException('No estás autorizado para eliminar este examen.');
        }

        $filepath = $exam->file_path;
        $deleted = $exam->delete(); // delete() devuelve boolean

        if ($deleted && $filepath && Storage::disk('public')->exists($filepath)) {
            Storage::disk('public')->delete($filepath);
        }

        return response()->json(['message' => 'Examen eliminado exitosamente.'], 200);
    }
}
