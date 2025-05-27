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

        // Obtener todos los exámenes y resoluciones
        $exams = Exam::all();
        $resolutions = Resolution::all();
        
        // Obtener usuarios para crear comentarios
        $users = User::role('user')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No se encontraron usuarios para crear comentarios.');
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

        // Para cada examen, crear algunos comentarios
        foreach ($exams as $exam) {
            // Crear entre 2 y 5 comentarios por examen
            $numComments = rand(2, 5);
            
            for ($i = 0; $i < $numComments; $i++) {
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

        // Para cada resolución, crear algunos comentarios
        foreach ($resolutions as $resolution) {
            // Crear entre 2 y 5 comentarios por resolución
            $numComments = rand(2, 5);
            
            for ($i = 0; $i < $numComments; $i++) {
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
    }
} 