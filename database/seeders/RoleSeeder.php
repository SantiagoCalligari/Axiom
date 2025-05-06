<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin_role = Role::query()->create([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);
        $admin_role->givePermissionTo('store university');
        $admin_role->givePermissionTo('delete university');
        $admin_role->givePermissionTo('store career');
        $admin_role->givePermissionTo('delete career');
        $admin_role->givePermissionTo('store exam');
        $admin_role->givePermissionTo('modify roles');
        $admin_role->givePermissionTo('modify user roles');

        $teacher_role = Role::query()->create([
            'name' => 'teacher',
            'guard_name' => 'api',
        ]);
        $teacher_role->givePermissionTo('store exam');

        $user_role = Role::query()->create([
            'name' => 'user',
            'guard_name' => 'api',
        ]);
        $user_role->givePermissionTo('store exam');
    }
}
