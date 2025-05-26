<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon; // Import Carbon for date manipulation

use App\Models\University;
use App\Models\Career;
use App\Models\Subject;
use App\Models\User;
use App\Models\Exam;
use Spatie\Permission\Models\Role;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a dummy user to upload exams
        $dummyUser = User::firstOrCreate(
            ['email' => 'seeder_user@axiom.test'],
            [
                'name' => 'Axiom Seeder',
                'password' => Hash::make('password'),
            ]
        );

        // Define the source path for the dummy PDF file
        $dummyPdfSourcePath = storage_path('app/seeder_files/dummy_exam.pdf');

        // Check if the dummy source file exists
        if (!File::exists($dummyPdfSourcePath)) {
            $this->command->error("Dummy PDF source file not found at: {$dummyPdfSourcePath}");
            $this->command->info("Please create a dummy PDF file (e.g., dummy_exam.pdf) at 'storage/app/seeder_files/'.");
            return;
        }

        // Get information about the dummy source file
        $dummyFileSize = File::size($dummyPdfSourcePath);
        $dummyMimeType = File::mimeType($dummyPdfSourcePath);

        // Define the storage disk and target directory
        $disk = Storage::disk('public');
        $examStoragePath = 'exams';

        // Ensure the target directory exists
        if (!$disk->exists($examStoragePath)) {
            $disk->makeDirectory($examStoragePath);
        }

        // Dummy professor names
        $professors = ['García', 'Martínez', 'López', 'González', 'Rodríguez', 'Pérez', 'Sánchez', 'Romero', 'Díaz', 'Torres'];

        $universities = [
            // ... (Tu array de universidades, carreras y materias se mantiene igual) ...
            [
                'name' => 'Universidad Nacional de Rosario',
                'description' => 'La Universidad Nacional de Rosario (UNR) es una universidad pública argentina con sede en la Ciudad de Rosario. Fue creada en 1968 y es una de las instituciones públicas más grandes de Argentina, con una amplia oferta académica y fuerte presencia en investigación.',
                'careers' => [
                    [
                        'name' => 'Medicina',
                        'subjects' => [
                            'Anatomía',
                            'Fisiología',
                            'Histología y Embriología',
                            'Farmacología',
                            'Patología'
                        ]
                    ],
                    [
                        'name' => 'Derecho',
                        'subjects' => [
                            'Introducción al Derecho',
                            'Derecho Penal I',
                            'Derecho Civil I (Parte General)',
                            'Derecho Constitucional',
                            'Derecho Administrativo'
                        ]
                    ],
                    [
                        'name' => 'Contador Público',
                        'subjects' => [
                            'Introducción a la Contabilidad',
                            'Matemática I',
                            'Introducción a la Economía',
                            'Sistemas de Información Contable',
                            'Derecho Comercial'
                        ]
                    ],
                    [
                        'name' => 'Arquitectura',
                        'subjects' => [
                            'Introducción al Diseño Arquitectónico',
                            'Representación Gráfica Arquitectónica',
                            'Historia de la Arquitectura I',
                            'Materiales y Tecnología',
                            'Sistemas Estructurales'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Civil',
                        'subjects' => [
                            'Estabilidad I',
                            'Mecánica de Suelos',
                            'Hormigón Armado I',
                            'Hidráulica General',
                            'Vías de Comunicación I'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Ciencia Política',
                        'subjects' => [
                            'Teoría Política I',
                            'Sistemas Políticos Comparados',
                            'Historia Política Argentina',
                            'Relaciones Internacionales',
                            'Sociología Política'
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Universidad Tecnológica Nacional Facultad Regional Rosario',
                'description' => 'La Facultad Regional Rosario (FRRo) de la Universidad Tecnológica Nacional (UTN) se especializa en la formación de profesionales en áreas de tecnología e ingeniería. Es parte de la red federal de la UTN.',
                'careers' => [
                    [
                        'name' => 'Ingeniería en Sistemas de Información',
                        'subjects' => [
                            'Algoritmos y Estructuras de Datos',
                            'Paradigmas de Programación',
                            'Bases de Datos',
                            'Sistemas Operativos',
                            'Ingeniería de Software'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Mecánica',
                        'subjects' => [
                            'Termodinámica',
                            'Mecánica Racional',
                            'Diseño de Máquinas I',
                            'Resistencia de Materiales',
                            'Mecánica de Fluidos'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Eléctrica',
                        'subjects' => [
                            'Circuitos Eléctricos I',
                            'Electrónica General',
                            'Máquinas Eléctricas I',
                            'Sistemas de Control',
                            'Instalaciones Eléctricas'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Química',
                        'subjects' => [
                            'Química General',
                            'Fisicoquímica',
                            'Operaciones Unitarias I',
                            'Reactores Químicos',
                            'Termodinámica Química'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Administración de Empresas',
                        'subjects' => [
                            'Administración General',
                            'Principios de Marketing',
                            'Gestión de Recursos Humanos',
                            'Finanzas Corporativas',
                            'Comercialización'
                        ]
                    ],
                    [
                        'name' => 'Tecnicatura Universitaria en Programación',
                        'subjects' => [
                            'Programación I',
                            'Laboratorio de Programación I',
                            'Arquitectura de Computadoras',
                            'Base de Datos',
                            'Metodología de la Programación'
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Pontificia Universidad Católica Argentina Sede Rosario',
                'description' => 'La sede Rosario de la Pontificia Universidad Católica Argentina (UCA) es una universidad privada con una oferta académica diversa, incluyendo facultades de Derecho, Ciencias Sociales, Ciencias Económicas, entre otras.',
                'careers' => [
                    [
                        'name' => 'Abogacía',
                        'subjects' => [
                            'Derecho Romano',
                            'Derecho Civil (Parte General)',
                            'Derecho Penal',
                            'Derecho de Familia',
                            'Derechos Humanos'
                        ]
                    ],
                    [
                        'name' => 'Contador Público',
                        'subjects' => [
                            'Contabilidad Superior',
                            'Auditoría',
                            'Sistemas Impositivos I',
                            'Finanzas de Empresas',
                            'Costos para la Gestión'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Comunicación Periodística',
                        'subjects' => [
                            'Teoría de la Comunicación',
                            'Géneros Periodísticos',
                            'Ética de la Comunicación',
                            'Redacción Periodística',
                            'Comunicación Digital'
                        ]
                    ],
                    [
                        'name' => 'Psicología',
                        'subjects' => [
                            'Psicología General',
                            'Neurociencias',
                            'Psicología del Desarrollo',
                            'Psicopatología',
                            'Técnicas de Evaluación Psicológica'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Industrial',
                        'subjects' => [
                            'Investigación Operativa',
                            'Gestión de la Producción',
                            'Ingeniería Económica',
                            'Control de Gestión',
                            'Logística y Cadena de Suministro'
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Universidad Católica de Santa Fe Sede Rosario',
                'description' => 'La Universidad Católica de Santa Fe (UCSF) cuenta con una sede en Rosario, ofreciendo carreras en diversas áreas como Arquitectura, Ciencias de la Salud y Psicología.',
                'careers' => [
                    [
                        'name' => 'Arquitectura',
                        'subjects' => [
                            'Taller de Arquitectura',
                            'Historia de la Arquitectura II',
                            'Instalaciones de Edificios',
                            'Estructuras I',
                            'Planeamiento Urbanístico'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Psicología',
                        'subjects' => [
                            'Historia de la Psicología',
                            'Psicoanálisis',
                            'Psicología Social',
                            'Neurofisiología',
                            'Psicología Cognitiva'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Obstetricia',
                        'subjects' => [
                            'Anatomofisiología del Embarazo',
                            'Semiología Obstétrica',
                            'Puericultura',
                            'Farmacología Obstétrica',
                            'Salud Pública Materno-Infantil'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Diseño Industrial',
                        'subjects' => [
                            'Diseño Industrial I',
                            'Morfología',
                            'Sistemas de Representación',
                            'Tecnología Industrial',
                            'Ergonomía'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Administración de Empresas Digitales',
                        'subjects' => [
                            'Administración de Empresas Digitales',
                            'Marketing Digital',
                            'E-commerce',
                            'Business Intelligence',
                            'Gestión de Proyectos Tecnológicos'
                        ]
                    ],
                ]
            ],
        ];

        $this->command->info('Starting University, Career, Subject, and Exam seeding...');

        foreach ($universities as $uniData) {
            $university = University::firstOrCreate(
                ['name' => $uniData['name']],
                ['slug' => Str::slug($uniData['name']), 'description' => $uniData['description']]
            );
            $this->command->info("Processing University: {$university->name}");

            foreach ($uniData['careers'] as $careerData) {
                $career = Career::firstOrCreate(
                    ['university_id' => $university->id, 'name' => $careerData['name']],
                    ['slug' => Str::slug($careerData['name'])]
                );
                $this->command->info("  Processing Career: {$career->name}");

                foreach ($careerData['subjects'] as $subjectName) {
                    $subjectSlugBase = Str::slug($subjectName);
                    $careerSlug = $career->slug;
                    $uniqueSubjectSlug = $subjectSlugBase . '-' . $careerSlug;
                    while (Subject::where('slug', $uniqueSubjectSlug)->exists()) {
                        $uniqueSubjectSlug .= '-' . Str::random(3);
                    }
                    $subject = Subject::firstOrCreate(
                        ['career_id' => $career->id, 'name' => $subjectName],
                        ['slug' => $uniqueSubjectSlug]
                    );
                    $this->command->info("    Processing Subject: {$subject->name} - Creating 50 exams...");

                    // --- Crear 50 Exámenes por Materia ---
                    // Inicializar la barra de progreso ANTES del bucle
                    $bar = $this->command->getOutput()->createProgressBar(50);
                    $bar->start();

                    // Bucle FOR para crear 50 exámenes
                    for ($i = 0; $i < 50; $i++) {
                        // Generar datos aleatorios para el examen
                        $randomProfessor = $professors[array_rand($professors)];
                        $randomYear = rand(date('Y') - 3, date('Y'));
                        $randomSemester = rand(1, 2) . 'C ' . $randomYear;
                        $randomIsResolved = (bool)rand(0, 1);

                        // Elegir aleatoriamente entre 'midterm', 'retake' o 'final'
                        $examTypes = ['midterm', 'retake', 'final'];
                        $randomExamType = $examTypes[array_rand($examTypes)];

                        $randomDate = Carbon::now()->subDays(rand(0, 365))->toDateString();
                        // Título más variado
                        $examTitle = "Examen " . Str::ucfirst($randomExamType) . " " . $subject->name . " #" . ($i + 1) . " - " . $randomSemester;

                        // Crear nombre de archivo único para la copia
                        $targetFileName = Str::slug($examTitle) . '-' . uniqid() . '.pdf';
                        $targetFilePath = $examStoragePath . '/' . $targetFileName;

                        // Copiar el archivo dummy
                        $stream = fopen($dummyPdfSourcePath, 'r');
                        if ($stream === false) {
                            $this->command->error("Failed to open dummy PDF source file: {$dummyPdfSourcePath}");
                            // Detener la barra y salir si falla la apertura del fuente
                            $bar->finish();
                            $this->command->newLine();
                            $this->command->error("Aborting exam creation for subject {$subject->name} due to file error.");
                            break; // Salir del bucle for
                        }
                        try {
                            $disk->put($targetFilePath, $stream);
                        } catch (\Exception $e) {
                            $this->command->error("Failed to copy dummy PDF to '{$targetFilePath}': " . $e->getMessage());
                            if (is_resource($stream)) fclose($stream);
                            // Detener la barra y salir si falla la copia
                            $bar->finish();
                            $this->command->newLine();
                            $this->command->error("Aborting exam creation for subject {$subject->name} due to file error.");
                            break; // Salir del bucle for
                        } finally {
                            if (is_resource($stream)) fclose($stream);
                        }

                        $examText = <<<'EOT'
# Facultad de Ciencias Exactas, Ingeniería y Agrimensura
## Departamento de Matemática - Escuela de Ciencias Exactas y Naturales
### Álgebra Lineal - LCC, LSI, PM - 2024

---

**Primera Evaluación Parcial - 22/04/2024**

**Apellido y nombres:** _________________ **Carrera:** _________________

---

### 1. Sean

$$\mathbf{u} = \begin{pmatrix} 1 \\ 4 \\ -3 \end{pmatrix}, \quad \mathbf{v} = \begin{pmatrix} 5 \\ -2 \\ 1 \end{pmatrix} \quad \text{donde } \mathbf{u}, \mathbf{v} \in \mathbb{R}^3$$

una función $T: \mathbb{R}_{2}[x] \to \mathbb{R}^4$ definida por $T(ax^2 + bx + c) = (a-c, 2a+b, a+b+c, b-c)$

**(a)** Pruebe que $V = \{p \in \mathbb{R}_2[x] : p(1) = 0\}$ es un $\mathbb{R}$-espacio vectorial (sin la suma y producto por escalar usuales de matrices).

**(b)** De una base $\mathcal{B}$ de $V$ y su dimensión. Justifique su respuesta.

**(c)** Pruebe que $T$ es una transformación lineal.

**(d)** Calcule $\ker T$ y $\text{Im} T$. Determine si $T$ es un monomorfismo, un epimorfismo o un isomorfismo. Justifique su respuesta.

**(e)** Halle la matriz asociada a $T$ respecto de la base $\mathcal{B}$ de $V$ (respecto que se base) y $\mathcal{B}_0$ (del $\mathbb{R}^4$) donde $T : V \to \mathbb{R}^4$.

---

### 2. Sea la base $\mathcal{B}^* = \{v_1, v_2, v_3\}$ donde

$v_1 = \begin{pmatrix} 1 \\ 1 \\ 1 \end{pmatrix}, \quad v_2 = \begin{pmatrix} 1 \\ 1 \\ 0 \end{pmatrix}, \quad v_3 = \begin{pmatrix} 1 \\ 0 \\ 1 \end{pmatrix}$

y la función $g: \mathbb{R}^3 \to \mathbb{R}^3$ tal que $g(x) = Ax$, donde $A \in \mathbb{R}^{3 \times 3}$ se dada por

$(x,y) = x_1y_1 - 2x_1y_2 - 4x_1y_3 + 2x_2y_1 + 4x_2y_2$

**(a)** Pruebe que la función $(\cdot,\cdot)$ define un producto interno en $\mathbb{R}^3$. De una matriz $G$ la base $\mathcal{B}_0$.

**(b)** Sea $\mathcal{S} = \{w_1, w_2, w_3\}$ donde $w_i$ de la base canónica $\{e_1, e_2, e_3\}$ y es que el producto interno $(\cdot, \cdot)$.

**(c)** Calcule la proyección de $v = (1, 1, 1)$ sobre $\mathcal{S}$.

---

### 3. Justifique las siguientes afirmaciones o dé un contraejemplo. Justifique su respuesta.

**(a)** Sea $U$ un $\mathcal{F}$-ev de dimensión $3$. Sean $T : U \to U$ de transformación $V$ de dimensión $4$ con intersección trivial. Entonces $(T + V)$ es de dimensión $7$.

**(b)** Sea $V$ un espacio vectorial real con producto interno $(\cdot, \cdot)$. Sean $B_1, B_2$ bases de $V$, sea la matriz del producto interno respecto de la base $B_1$ es $G_1$ y respecto de la base $B_2$ es $G_2$, ¿es $G_1 = G_2$?

**(c)** Si $U$ y $W$ son dos subespacios de un espacio euclidiano $V$ de dimensión finita tal que se suman de manera directa entonces $(U \oplus W)^{\perp} = U^{\perp} \cap W^{\perp}$.

**(d)** Sea $T \in \mathcal{L}(X, Y)$. Entonces la función $\|T\| : \mathbb{R}^+ \to \mathbb{R}^+$. $A$ define $\|T(x)\| = \|T\| \cdot \|x\|$ es una forma bilineal.

---
EOT;
                        // Crear el registro del examen
                        Exam::create([
                            'user_id' => $dummyUser->id,
                            'subject_id' => $subject->id,
                            'title' => $examTitle,
                            'professor_name' => $randomProfessor,
                            'semester' => $randomSemester,
                            'year' => $randomYear,
                            'is_resolved' => $randomIsResolved,
                            'exam_type' => $randomExamType,
                            'exam_date' => $randomDate,
                            'file_path' => $targetFilePath,
                            'original_file_name' => $targetFileName,
                            'mime_type' => $dummyMimeType,
                            'file_size' => $dummyFileSize,
                            'ocr_text' => $examText,
                            // 'slug' => Str::slug($examTitle . '-' . uniqid()) // Generar slug si es necesario
                        ]);

                        $bar->advance(); // Avanzar la barra de progreso DENTRO del bucle for
                    } // Fin del bucle for

                    // Finalizar la barra de progreso DESPUÉS del bucle for
                    $bar->finish();
                    $this->command->newLine(); // Nueva línea después de la barra de progreso
                    // --- Fin Crear 50 Exámenes ---
                }
            }
        }
        $this->command->info('University, Career, Subject, User, and Exam seeding complete.');
    }
}
