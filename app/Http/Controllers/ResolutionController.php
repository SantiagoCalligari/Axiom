<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\University;
use App\Models\Career;
use App\Models\Subject;
use App\Models\Resolution;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ResolutionResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ResolutionRequest;

class ResolutionController extends Controller
{
    public function store(ResolutionRequest $request, University $university, Career $career, Subject $subject, Exam $exam)
    {
        // Verificar si ya existe una resolución
        if ($exam->resolution()->exists()) {
            return response()->json(['message' => 'Este examen ya tiene una resolución'], 409);
        }

        $file = $request->file('file');
        $path = $file->store('resolutions', 'public');

        // Asegurarnos de que el archivo se guardó correctamente
        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'Error al guardar el archivo'], 500);
        }

        $resolution = Resolution::create([
            'exam_id' => $exam->id,
            'user_id' => Auth::id(),
            'file_path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Verificar que la URL de descarga se genera correctamente
        $downloadUrl = $resolution->download_url;
        if (!$downloadUrl) {
            return response()->json(['message' => 'Error al generar la URL de descarga'], 500);
        }

        $exam->is_resolved = true;
        $exam->save();
        return new ResolutionResource($resolution);
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university, Career $career, Subject $subject, Exam $exam)
    {
        $resolution = $exam->resolution()->with('uploader')->first();
        if (!$resolution) {
            return response()->json(['message' => 'No hay resolución disponible para este examen'], 404);
        }
        return new ResolutionResource($resolution);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResolutionRequest $request, University $university, Career $career, Subject $subject, Exam $exam)
    {
        $resolution = $exam->resolution;
        if (!$resolution) {
            return response()->json(['message' => 'No hay resolución disponible para este examen'], 404);
        }

        if ($resolution->user_id !== Auth::id()) {
            abort(403);
        }

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($resolution->file_path);
            $file = $request->file('file');
            $path = $file->store('resolutions', 'public');

            $resolution->update([
                'file_path' => $path,
                'original_file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return new ResolutionResource($resolution);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university, Career $career, Subject $subject, Exam $exam)
    {
        $resolution = $exam->resolution;
        if (!$resolution) {
            return response()->json(['message' => 'No hay resolución disponible para este examen'], 404);
        }

        if ($resolution->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($resolution->file_path);
        $resolution->delete();

        return response()->noContent();
    }
}
