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
        $admin_role->givePermissionTo('store universities');
        $admin_role->givePermissionTo('store careers');
        $admin_role->givePermissionTo('store exams');
        $admin_role->givePermissionTo('modify roles');

        $teacher_role = Role::query()->create([
            'name' => 'teacher',
            'guard_name' => 'api',
        ]);
        $teacher_role->givePermissionTo('store exams');

        $user_role = Role::query()->create([
            'name' => 'user',
            'guard_name' => 'api',
        ]);
        $user_role->givePermissionTo('store exams');
    }
}
