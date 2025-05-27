<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ResolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = Exam::where('is_resolved', true)->get();
        $teachers = User::role('teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command->info('No hay profesores disponibles para crear resoluciones.');
            return;
        }

        // Crear un archivo PDF de ejemplo
        $exampleContent = '%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>
endobj
4 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
5 0 obj
<< /Length 44 >>
stream
BT /F1 12 Tf 100 700 Td (Ejemplo de Resolucion) Tj ET
endstream
endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000056 00000 n
0000000111 00000 n
0000000212 00000 n
0000000279 00000 n
trailer
<< /Size 6 /Root 1 0 R >>
startxref
364
%%EOF';

        // Asegurarnos de que el directorio existe
        Storage::disk('public')->makeDirectory('resolutions');

        foreach ($exams as $exam) {
            // Crear un archivo único para cada resolución
            $fileName = 'resolutions/resolucion_' . $exam->id . '.pdf';
            Storage::disk('public')->put($fileName, $exampleContent);

            // Crear la resolución en la base de datos
            Resolution::create([
                'exam_id' => $exam->id,
                'user_id' => $teachers->random()->id,
                'file_path' => $fileName,
                'original_file_name' => 'resolucion_ejemplo.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => strlen($exampleContent),
                'comments' => 'Esta es una resolución de ejemplo para el examen ' . $exam->title,
            ]);
        }
    }
}
