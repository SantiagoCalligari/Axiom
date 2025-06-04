<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;

class PendingExamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las materias
        $subjects = Subject::all();
        
        // Obtener algunos usuarios normales para subir los exámenes
        $users = User::role('user')->take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No hay usuarios normales para subir exámenes. Por favor, crea algunos usuarios primero.');
            return;
        }

        foreach ($subjects as $subject) {
            // Crear 2 exámenes pendientes por materia
            for ($i = 0; $i < 2; $i++) {
                $user = $users->random();
                
                Exam::create([
                    'user_id' => $user->id,
                    'subject_id' => $subject->id,
                    'title' => "Examen Parcial " . ($i + 1) . " - " . fake()->words(3, true),
                    'professor_name' => fake()->name(),
                    'semester' => fake()->numberBetween(1, 2),
                    'year' => fake()->year(),
                    'is_resolved' => fake()->boolean(),
                    'exam_type' => fake()->randomElement(['parcial', 'final', 'recuperatorio']),
                    'exam_date' => fake()->date(),
                    'approval_status' => 'pending',
                    'file_path' => 'exams/placeholder.pdf', // Asumiendo que existe un archivo placeholder
                    'original_file_name' => 'examen.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size' => fake()->numberBetween(100000, 5000000),
                ]);
            }
        }

        $this->command->info('Se han creado ' . ($subjects->count() * 2) . ' exámenes pendientes de aprobación.');
    }
}
