<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Comment seeding...');

        // Obtener todos los usuarios para usar como autores de comentarios
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Obtener todos los exámenes
        $exams = Exam::all();
        if ($exams->isEmpty()) {
            $this->command->error('No exams found. Please run UniversitySeeder first.');
            return;
        }

        // Contenido de ejemplo para los comentarios
        $commentContents = [
            '¡Excelente examen! Muy completo y bien estructurado.',
            'Algunas preguntas estaban un poco ambiguas, pero en general está bien.',
            '¿Alguien tiene la resolución de este examen?',
            'El profesor es muy exigente con la notación matemática.',
            'Este examen es más fácil que el del año pasado.',
            '¿Alguien puede explicar cómo resolver el ejercicio 3?',
            'La parte de teoría estaba muy completa.',
            'Me ayudó mucho estudiar con este examen.',
            '¿Tienen el material de estudio que usaron para preparar este examen?',
            'El ejercicio 2 es similar al que vimos en clase.',
            'LaTeX: $E = mc^2$ es la ecuación más famosa de la física.',
            'LaTeX: $$\int_{a}^{b} f(x) dx$$ es la integral definida.',
            'LaTeX: $$\frac{d}{dx}f(x) = \lim_{h \to 0}\frac{f(x+h) - f(x)}{h}$$ es la definición de derivada.',
            'LaTeX: $$\sum_{i=1}^{n} i = \frac{n(n+1)}{2}$$ es la suma de los primeros n números naturales.',
            'LaTeX: $$\begin{pmatrix} a & b \\ c & d \end{pmatrix}$$ es una matriz 2x2.',
        ];

        // Barra de progreso para exámenes
        $examBar = $this->command->getOutput()->createProgressBar($exams->count());
        $examBar->start();

        foreach ($exams as $exam) {
            // Generar entre 2 y 5 comentarios principales por examen
            $numMainComments = rand(2, 5);
            
            for ($i = 0; $i < $numMainComments; $i++) {
                $mainComment = Comment::create([
                    'user_id' => $users->random()->id,
                    'exam_id' => $exam->id,
                    'parent_id' => null,
                    'content' => $commentContents[array_rand($commentContents)],
                    'upvotes' => rand(0, 10),
                    'downvotes' => rand(0, 3),
                ]);

                // Generar entre 0 y 3 respuestas para cada comentario principal
                $numReplies = rand(0, 3);
                for ($j = 0; $j < $numReplies; $j++) {
                    Comment::create([
                        'user_id' => $users->random()->id,
                        'exam_id' => $exam->id,
                        'parent_id' => $mainComment->id,
                        'content' => $commentContents[array_rand($commentContents)],
                        'upvotes' => rand(0, 5),
                        'downvotes' => rand(0, 2),
                    ]);
                }
            }

            $examBar->advance();
        }

        $examBar->finish();
        $this->command->newLine();
        $this->command->info('Comment seeding completed successfully!');
    }
} 