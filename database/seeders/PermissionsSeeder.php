<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::query()->create([
            'name' => 'store universities',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'store careers',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'store exams',
            'guard_name' => 'api',
        ]);
    }
}
