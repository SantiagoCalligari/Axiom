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
        // Eliminar permisos existentes
        Permission::query()->delete();

        Permission::query()->create([
            'name' => 'store university',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'delete university',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'store career',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'delete career',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'modify roles',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'store exam',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'modify user roles',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'manage university admins',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'manage career admins',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'manage subject admins',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'manage resolutions',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'manage comments',
            'guard_name' => 'api',
        ]);
        Permission::query()->create([
            'name' => 'vote comments',
            'guard_name' => 'api',
        ]);
    }
}
