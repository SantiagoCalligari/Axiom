<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $roles = [
            Role::ADMIN => 'Administrador General',
            Role::UNIVERSITY_ADMIN => 'Administrador de Universidad',
            Role::CAREER_ADMIN => 'Administrador de Carrera',
            Role::SUBJECT_ADMIN => 'Administrador de Materia',
            Role::TEACHER => 'Profesor',
            Role::STUDENT => 'Estudiante',
        ];

        foreach ($roles as $key => $name) {
            Role::firstOrCreate(['name' => $key], ['display_name' => $name]);
        }

        // Crear usuario admin general
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin General',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole(Role::ADMIN);

        // Crear usuario admin de universidad
        $universityAdmin = User::firstOrCreate(
            ['email' => 'university_admin@example.com'],
            [
                'name' => 'Admin Universidad',
                'password' => Hash::make('password'),
            ]
        );
        $universityAdmin->assignRole(Role::UNIVERSITY_ADMIN);

        // Crear usuario admin de carrera
        $careerAdmin = User::firstOrCreate(
            ['email' => 'career_admin@example.com'],
            [
                'name' => 'Admin Carrera',
                'password' => Hash::make('password'),
            ]
        );
        $careerAdmin->assignRole(Role::CAREER_ADMIN);

        // Crear usuario admin de materia
        $subjectAdmin = User::firstOrCreate(
            ['email' => 'subject_admin@example.com'],
            [
                'name' => 'Admin Materia',
                'password' => Hash::make('password'),
            ]
        );
        $subjectAdmin->assignRole(Role::SUBJECT_ADMIN);

        // Crear usuario profesor
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'name' => 'Profesor',
                'password' => Hash::make('password'),
            ]
        );
        $teacher->assignRole(Role::TEACHER);

        // Crear más profesores
        $teachers = User::factory(5)->create();
        foreach ($teachers as $user) {
            $user->assignRole(Role::TEACHER);
        }

        // Crear usuarios estudiantes
        $students = User::factory(10)->create();
        foreach ($students as $user) {
            $user->assignRole(Role::STUDENT);
        }

        // Asignar rol de estudiante a todos los usuarios que no tengan roles
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();
        foreach ($usersWithoutRoles as $user) {
            $user->assignRole(Role::STUDENT);
        }
    }
}
