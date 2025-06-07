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
            Role::STUDENT => 'Estudiante',
        ];

        foreach ($roles as $key => $name) {
            Role::firstOrCreate(['name' => $key], ['display_name' => $name]);
        }

        // Crear usuario admin si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole(Role::ADMIN);

        // Crear usuario estudiante si no existe
        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Estudiante',
                'password' => Hash::make('password'),
            ]
        );
        $student->assignRole(Role::STUDENT);

        // Crear usuarios normales
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->assignRole(Role::STUDENT);
        }

        // Asignar rol de estudiante a todos los usuarios que no tengan roles
        $usersWithoutRoles = User::whereDoesntHave('roles')->get();
        foreach ($usersWithoutRoles as $user) {
            $user->assignRole(Role::STUDENT);
        }
    }
}
