<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $admin */
        $admin = User::query()->create([
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'name' => 'admin',
        ]);
        $admin->assignRole('admin');
        User::query()->create([
            'email' => 'santiago@calligari.ar',
            'password' => bcrypt('muriel'),
            'name' => 'Santiago Calligari',
        ]);

        // Crear algunos profesores
        $teachers = [
            [
                'email' => 'profesor1@example.com',
                'password' => bcrypt('password'),
                'name' => 'Profesor Uno',
            ],
            [
                'email' => 'profesor2@example.com',
                'password' => bcrypt('password'),
                'name' => 'Profesor Dos',
            ],
            [
                'email' => 'profesor3@example.com',
                'password' => bcrypt('password'),
                'name' => 'Profesor Tres',
            ],
        ];

        foreach ($teachers as $teacher) {
            $user = User::query()->create($teacher);
            $user->assignRole('teacher');
        }
    }
}
