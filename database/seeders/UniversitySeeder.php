<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\University;
use App\Models\Career;
use App\Models\Subject; // Import the Subject model

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = [
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
                            'Introducción al Derecho', // Based on search results [13]
                            'Derecho Penal I',
                            'Derecho Civil I (Parte General)',
                            'Derecho Constitucional', // Based on search results [13]
                            'Derecho Administrativo' // Based on search results [13]
                        ]
                    ],
                    [
                        'name' => 'Contador Público',
                        'subjects' => [
                            'Introducción a la Contabilidad', // Based on search results [13, 21, 28]
                            'Matemática I', // Based on search results [13, 28]
                            'Introducción a la Economía', // Based on search results [13, 28]
                            'Sistemas de Información Contable', // Based on search results [13]
                            'Derecho Comercial' // Based on search results [13]
                        ]
                    ],
                    [
                        'name' => 'Arquitectura', // Based on search results [24]
                        'subjects' => [
                            'Introducción al Diseño Arquitectónico',
                            'Representación Gráfica Arquitectónica',
                            'Historia de la Arquitectura I',
                            'Materiales y Tecnología',
                            'Sistemas Estructurales'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Civil', // Based on search results [27, 33]
                        'subjects' => [
                            'Estabilidad I',
                            'Mecánica de Suelos',
                            'Hormigón Armado I',
                            'Hidráulica General', // Based on search results [33]
                            'Vías de Comunicación I' // Based on search results [3, 14, 18]
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Ciencia Política', // Based on search results [15]
                        'subjects' => [
                            'Teoría Política I',
                            'Sistemas Políticos Comparados',
                            'Historia Política Argentina', // Based on search results [26]
                            'Relaciones Internacionales', // Based on search results [15]
                            'Sociología Política'
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Universidad Tecnológica Nacional Facultad Regional Rosario',
                'description' => 'La Facultad Regional Rosario (FRRo) de la Universidad Tecnológica Nacional (UTN) se especializa en la formación de profesionales en áreas de tecnología e ingeniería. Es parte de la red federal de la UTN.', // Based on search results [34, 46, 47, 48]
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
                        'name' => 'Ingeniería Mecánica', // Based on search results [27, 33]
                        'subjects' => [
                            'Termodinámica', // Based on search results [30, 33]
                            'Mecánica Racional',
                            'Diseño de Máquinas I',
                            'Resistencia de Materiales',
                            'Mecánica de Fluidos' // Based on search results [4, 9]
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Eléctrica', // Based on search results [27, 33]
                        'subjects' => [
                            'Circuitos Eléctricos I',
                            'Electrónica General',
                            'Máquinas Eléctricas I', // Based on search results [33]
                            'Sistemas de Control',
                            'Instalaciones Eléctricas'
                        ]
                    ],
                    [
                        'name' => 'Ingeniería Química', // Based on search results [12, 45]
                        'subjects' => [
                            'Química General', // Based on search results [9, 12]
                            'Fisicoquímica', // Based on search results [12, 50]
                            'Operaciones Unitarias I', // Based on search results [12]
                            'Reactores Químicos', // Based on search results [12]
                            'Termodinámica Química' // Based on search results [12, 43]
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Administración de Empresas', // Based on search results [11, 35, 40]
                        'subjects' => [
                            'Administración General', // Based on search results [11, 28]
                            'Principios de Marketing',
                            'Gestión de Recursos Humanos', // Based on search results [28, 40]
                            'Finanzas Corporativas',
                            'Comercialización' // Based on search results [28, 35]
                        ]
                    ],
                    [
                        'name' => 'Tecnicatura Universitaria en Programación', // Based on search results [10, 42, 46, 47, 51]
                        'subjects' => [
                            'Programación I', // Based on search results [10]
                            'Laboratorio de Programación I',
                            'Arquitectura de Computadoras',
                            'Base de Datos', // Based on search results [10]
                            'Metodología de la Programación'
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Pontificia Universidad Católica Argentina Sede Rosario',
                'description' => 'La sede Rosario de la Pontificia Universidad Católica Argentina (UCA) es una universidad privada con una oferta académica diversa, incluyendo facultades de Derecho, Ciencias Sociales, Ciencias Económicas, entre otras.', // Based on search results [7, 23]
                'careers' => [
                    [
                        'name' => 'Abogacía', // Similar to Derecho
                        'subjects' => [
                            'Derecho Romano', // Based on search results [25]
                            'Derecho Civil (Parte General)',
                            'Derecho Penal',
                            'Derecho de Familia',
                            'Derechos Humanos' // Based on search results [25]
                        ]
                    ],
                    [
                        'name' => 'Contador Público', // Based on search results [7, 17]
                        'subjects' => [
                            'Contabilidad Superior',
                            'Auditoría', // Based on search results [7, 17]
                            'Sistemas Impositivos I', // Based on search results [17]
                            'Finanzas de Empresas',
                            'Costos para la Gestión' // Based on search results [7]
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
                        'name' => 'Ingeniería Industrial', // Based on search results [4, 6, 9, 30]
                        'subjects' => [
                            'Investigación Operativa',
                            'Gestión de la Producción',
                            'Ingeniería Económica', // Based on search results [9]
                            'Control de Gestión',
                            'Logística y Cadena de Suministro' // Based on search results [4]
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Universidad Católica de Santa Fe Sede Rosario',
                'description' => 'La Universidad Católica de Santa Fe (UCSF) cuenta con una sede en Rosario, ofreciendo carreras en diversas áreas como Arquitectura, Ciencias de la Salud y Psicología.', // Based on search results [37, 41, 44]
                'careers' => [
                    [
                        'name' => 'Arquitectura', // Based on search results [37, 41]
                        'subjects' => [
                            'Taller de Arquitectura',
                            'Historia de la Arquitectura II',
                            'Instalaciones de Edificios',
                            'Estructuras I',
                            'Planeamiento Urbanístico'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Psicología', // Based on search results [28]
                        'subjects' => [
                            'Historia de la Psicología',
                            'Psicoanálisis',
                            'Psicología Social',
                            'Neurofisiología',
                            'Psicología Cognitiva'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Obstetricia', // Based on search results [28]
                        'subjects' => [
                            'Anatomofisiología del Embarazo',
                            'Semiología Obstétrica',
                            'Puericultura',
                            'Farmacología Obstétrica',
                            'Salud Pública Materno-Infantil'
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Diseño Industrial', // Based on search results [5, 36, 37, 41, 44]
                        'subjects' => [
                            'Diseño Industrial I', // Based on search results [5]
                            'Morfología', // Based on search results [5]
                            'Sistemas de Representación', // Based on search results [5]
                            'Tecnología Industrial', // Based on search results [5]
                            'Ergonomía' // Based on search results [5]
                        ]
                    ],
                    [
                        'name' => 'Licenciatura en Administración de Empresas Digitales', // Based on search results [28] - Assuming similar to Admin.
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

        foreach ($universities as $uniData) {
            $university = University::create([
                'name' => $uniData['name'],
                'slug' => Str::slug($uniData['name']),
                'description' => $uniData['description'],
            ]);

            foreach ($uniData['careers'] as $careerData) {
                // Ensure we don't create too many careers if the source provided more than 5 (though unlikely with structured data)
                if (Career::where('university_id', $university->id)->count() < 6) { // Allowing up to 6 as some lists had more initially
                    $career = Career::create([
                        'name' => $careerData['name'],
                        'university_id' => $university->id,
                        // Career slug unique per university (assuming your migration has the composite index)
                        'slug' => Str::slug($careerData['name']),
                    ]);

                    $subjectCount = 0; // Counter to limit subjects per career

                    foreach ($careerData['subjects'] as $subjectName) {
                        if ($subjectCount < 5) { // Limit to 5 subjects per career
                            // Generate a globally unique slug for the subject
                            // Combining subject slug and career slug
                            $subjectSlug = Str::slug($subjectName);
                            $careerSlug = $career->slug; // Get the slug of the created career
                            $uniqueSubjectSlug = $subjectSlug . '-' . $careerSlug;

                            // Ensure the subject slug is truly unique in case of accidental duplicates
                            while (Subject::where('slug', $uniqueSubjectSlug)->exists()) {
                                $uniqueSubjectSlug .= '-' . Str::random(3); // Append random string if slug exists
                            }


                            Subject::create([
                                'name' => $subjectName,
                                'career_id' => $career->id,
                                'slug' => $uniqueSubjectSlug, // Use the generated unique slug
                                // 'description' => null, // Subjects array doesn't have descriptions, leaving as null or add simple generic one
                            ]);

                            $subjectCount++;
                        }
                    }
                }
            }
        }
    }
}
