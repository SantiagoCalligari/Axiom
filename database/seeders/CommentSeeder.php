<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Exam;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar comentarios existentes
        Comment::query()->delete();

        // Obtener usuarios para crear comentarios
        $users = User::role('student')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No se encontraron estudiantes para crear comentarios.');
            return;
        }
        
        // Frases de ejemplo para los comentarios
        $commentPhrases = [
            '¿Podrías explicar mejor este punto?',
            'No entiendo bien esta parte',
            'Sería útil tener más ejemplos',
            '¿Hay material adicional recomendado?',
            '¿Cuál es el criterio de evaluación?',
            '¿Se puede consultar bibliografía?',
            '¿Hay ejercicios similares para practicar?',
            '¿Cuánto tiempo se recomienda dedicar a cada sección?',
            '¿Se permiten calculadoras?',
            '¿Hay algún tema específico que se deba enfatizar?'
        ];

        // Procesar exámenes en lotes de 100
        Exam::query()->chunk(100, function ($exams) use ($users, $commentPhrases) {
            foreach ($exams as $exam) {
                // Crear exactamente 2 comentarios por examen
                for ($i = 0; $i < 2; $i++) {
                    Comment::create([
                        'exam_id' => $exam->id,
                        'user_id' => $users->random()->id,
                        'content' => $commentPhrases[array_rand($commentPhrases)],
                        'comment_type' => 'exam',
                        'upvotes' => rand(0, 10),
                        'downvotes' => rand(0, 5)
                    ]);
                }
            }
        });

        // Procesar resoluciones en lotes de 100
        Resolution::query()->chunk(100, function ($resolutions) use ($users, $commentPhrases) {
            foreach ($resolutions as $resolution) {
                // Crear exactamente 2 comentarios por resolución
                for ($i = 0; $i < 2; $i++) {
                    Comment::create([
                        'exam_id' => $resolution->exam_id,
                        'user_id' => $users->random()->id,
                        'content' => $commentPhrases[array_rand($commentPhrases)],
                        'comment_type' => 'resolution',
                        'upvotes' => rand(0, 10),
                        'downvotes' => rand(0, 5)
                    ]);
                }
            }
        });
    }
} 