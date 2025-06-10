<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PendingExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las materias
        $subjects = Subject::all();
        
        // Obtener algunos usuarios estudiantes para subir los exámenes
        $users = User::role('student')->take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No hay estudiantes para subir exámenes. Por favor, crea algunos estudiantes primero.');
            return;
        }

        // Definir la ruta del archivo dummy
        $dummyPdfSourcePath = storage_path('app/seeder_files/dummy_exam.pdf');

        // Verificar si existe el archivo dummy
        if (!File::exists($dummyPdfSourcePath)) {
            $this->command->error("Archivo dummy PDF no encontrado en: {$dummyPdfSourcePath}");
            return;
        }

        // Obtener información del archivo dummy
        $dummyFileSize = File::size($dummyPdfSourcePath);
        $dummyMimeType = File::mimeType($dummyPdfSourcePath);

        // Definir el disco de almacenamiento y directorio
        $disk = Storage::disk('public');
        $examStoragePath = 'exams';

        // Asegurar que el directorio existe
        if (!$disk->exists($examStoragePath)) {
            $disk->makeDirectory($examStoragePath);
        }

        foreach ($subjects as $subject) {
            // Crear 2 exámenes pendientes por materia
            for ($i = 0; $i < 2; $i++) {
                $user = $users->random();
                
                // Generar título y nombre de archivo
                $examTitle = "Examen Parcial " . ($i + 1) . " - " . fake()->words(3, true);
                $targetFileName = Str::slug($examTitle) . '-' . uniqid() . '.pdf';
                $targetFilePath = $examStoragePath . '/' . $targetFileName;

                // Copiar el archivo dummy
                try {
                    $disk->put($targetFilePath, File::get($dummyPdfSourcePath));
                } catch (\Exception $e) {
                    $this->command->error("Error al copiar el archivo dummy: " . $e->getMessage());
                    continue;
                }

                // Verificar que el archivo se copió correctamente
                if (!$disk->exists($targetFilePath)) {
                    $this->command->error("Error: El archivo no se copió correctamente a {$targetFilePath}");
                    continue;
                }

                Exam::create([
                    'user_id' => $user->id,
                    'subject_id' => $subject->id,
                    'title' => $examTitle,
                    'professor_name' => fake()->name(),
                    'semester' => fake()->numberBetween(1, 2) . 'C ' . fake()->year(),
                    'year' => fake()->year(),
                    'is_resolved' => fake()->boolean(),
                    'exam_type' => fake()->randomElement(['midterm', 'final', 'retake']),
                    'exam_date' => fake()->date(),
                    'approval_status' => 'pending',
                    'file_path' => $targetFilePath,
                    'original_file_name' => $targetFileName,
                    'mime_type' => $dummyMimeType,
                    'file_size' => $dummyFileSize,
                ]);
            }
        }

        $this->command->info('Se han creado ' . ($subjects->count() * 2) . ' exámenes pendientes de aprobación.');
    }
}
