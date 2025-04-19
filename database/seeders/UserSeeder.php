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
    }
}
