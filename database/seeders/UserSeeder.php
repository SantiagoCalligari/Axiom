<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar usuarios existentes
        User::query()->delete();

        // Admin
        $admin = User::query()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'name' => 'Administrador',
        ]);
        $admin->assignRole('admin');

        // University Admin
        $universityAdmin = User::query()->create([
            'email' => 'university@admin.com',
            'password' => bcrypt('university'),
            'name' => 'Admin Universidad',
        ]);
        $universityAdmin->assignRole('university_admin');

        // Career Admin
        $careerAdmin = User::query()->create([
            'email' => 'career@admin.com',
            'password' => bcrypt('career'),
            'name' => 'Admin Carrera',
        ]);
        $careerAdmin->assignRole('career_admin');

        // Subject Admin
        $subjectAdmin = User::query()->create([
            'email' => 'subject@admin.com',
            'password' => bcrypt('subject'),
            'name' => 'Admin Materia',
        ]);
        $subjectAdmin->assignRole('subject_admin');

        // Teacher
        $teacher = User::query()->create([
            'email' => 'teacher@example.com',
            'password' => bcrypt('teacher'),
            'name' => 'Profesor',
        ]);
        $teacher->assignRole('teacher');

        // Regular User
        $user = User::query()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'name' => 'Usuario Regular',
        ]);
        $user->assignRole('user');
        $user = User::query()->create([
            'email' => 'santiago@calligari.ar',
            'password' => bcrypt('muriel'),
            'name' => 'Santiago Calligari',
        ]);
        $user->assignRole('user');
    }
}
