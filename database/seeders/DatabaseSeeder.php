<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            UniversitySeeder::class,
            CommentSeeder::class,
            ResolutionSeeder::class,
            PendingExamsSeeder::class,
        ]);
    }
}
