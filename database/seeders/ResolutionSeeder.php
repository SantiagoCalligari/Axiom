<?php

namespace Database\Seeders;

use App\Models\Resolution;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ResolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eliminar resoluciones existentes
        Resolution::query()->delete();

        // Obtener todos los exámenes
        $exams = Exam::all();
        
        // Obtener un profesor para asignar las resoluciones
        $teacher = User::role('teacher')->first();
        
        if (!$teacher) {
            $this->command->error('No se encontró ningún profesor para asignar las resoluciones.');
            return;
        }

        // Para cada examen, crear una resolución
        foreach ($exams as $exam) {
            Resolution::create([
                'exam_id' => $exam->id,
                'user_id' => $teacher->id,
                'file_path' => 'resolutions/example.pdf',
                'original_file_name' => 'resolucion_ejemplo.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 1024,
                'comments' => 'Esta es una resolución de ejemplo para el examen de ' . $exam->subject->name
            ]);
        }
    }
}
