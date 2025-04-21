<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\University;
use App\Models\Career;

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
                'description' => 'La Universidad Nacional de Rosario (UNR) es una universidad pública argentina con sede en la Ciudad de Rosario. Fue creada en 1968 y es una de las instituciones públicas más grandes de Argentina, con una amplia oferta académica y fuerte presencia en investigación.', // Based on search results [3, 9, 11, 12]
                'careers' => [
                    'Medicina', // Popular career [23, 30]
                    'Derecho', // Popular career [23, 30, 34]
                    'Contador Público', // Popular career [30, 34]
                    'Arquitectura', // Offered by UNR [14]
                    'Ingeniería Civil', // Offered by UNR [14]
                    'Licenciatura en Ciencias de la Computacion', // Offered by UNR [14]
                ]
            ],
            [
                'name' => 'Universidad Tecnológica Nacional Facultad Regional Rosario',
                'description' => 'La Facultad Regional Rosario (FRRo) de la Universidad Tecnológica Nacional (UTN) se especializa en la formación de profesionales en áreas de tecnología e ingeniería. Es parte de la red federal de la UTN.', // Based on search results [4, 5, 22, 25]
                'careers' => [
                    'Ingeniería en Sistemas de Información', // Offered by UTN-FRRO [4, 5, 7, 39]
                    'Ingeniería Mecánica', // Offered by UTN-FRRO [4, 5, 7, 39]
                    'Ingeniería Eléctrica', // Offered by UTN-FRRO [4, 5, 7, 39]
                    'Ingeniería Química', // Offered by UTN-FRRO [4, 5, 7, 39]
                    'Licenciatura en Administración de Empresas', // Offered by UTN-FRRO [4]
                    'Tecnicatura Universitaria en Programación', // Offered by UTN-FRRO [4]
                ]
            ],
            [
                'name' => 'Pontificia Universidad Católica Argentina Sede Rosario',
                'description' => 'La sede Rosario de la Pontificia Universidad Católica Argentina (UCA) es una universidad privada con una oferta académica diversa, incluyendo facultades de Derecho, Ciencias Sociales, Ciencias Económicas, entre otras.', // Based on search results [6, 8, 10, 19, 21, 32]
                'careers' => [
                    'Abogacía', // Offered by UCA Rosario [17, 32]
                    'Contador Público', // Offered by UCA Rosario [31]
                    'Licenciatura en Comunicación Periodística', // Offered by UCA Rosario [8]
                    'Psicología', // Offered by UCA Rosario (Faculty exists) [32] - Also a popular career [30]
                    'Ingeniería Industrial', // Offered by UCA Rosario [17, 31]
                ]
            ],
            [
                'name' => 'Universidad Católica de Santa Fe Sede Rosario',
                'description' => 'La Universidad Católica de Santa Fe (UCSF) cuenta con una sede en Rosario, ofreciendo carreras en diversas áreas como Arquitectura, Ciencias de la Salud y Psicología.', // Based on search results [28]
                'careers' => [
                    'Arquitectura', // Offered by UCSF Rosario [28]
                    'Licenciatura en Psicología', // Offered by UCSF Rosario [28]
                    'Licenciatura en Obstetricia', // Offered by UCSF Rosario [28]
                    'Licenciatura en Diseño Industrial', // Offered by UCSF Rosario [28]
                    'Licenciatura en Administración de Empresas Digitales', // Offered by UCSF Rosario (A Distancia) [28]
                ]
            ],
        ];

        foreach ($universities as $uniData) {
            $university = University::create([
                'name' => $uniData['name'],
                'slug' => Str::slug($uniData['name']),
                'description' => $uniData['description'],
            ]);

            foreach ($uniData['careers'] as $careerName) {
                // Ensure we don't create too many careers if the source provided more than 5
                if (Career::where('university_id', $university->id)->count() < 5) {
                    Career::create([
                        'name' => $careerName,
                        'university_id' => $university->id,
                    ]);
                }
            }
        }
    }
}
