<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar roles existentes
        Role::query()->delete();

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
        $admin_role->givePermissionTo('manage resolutions');
        $admin_role->givePermissionTo('manage comments');

        $teacher_role = Role::query()->create([
            'name' => 'teacher',
            'guard_name' => 'api',
        ]);
        $teacher_role->givePermissionTo('store exam');
        $teacher_role->givePermissionTo('manage resolutions');
        $teacher_role->givePermissionTo('manage comments');

        $user_role = Role::query()->create([
            'name' => 'user',
            'guard_name' => 'api',
        ]);
        $user_role->givePermissionTo('store exam');
        $user_role->givePermissionTo('vote comments');

        $university_admin_role = Role::query()->create([
            'name' => 'university_admin',
            'guard_name' => 'api',
        ]);
        $university_admin_role->givePermissionTo('store university');
        $university_admin_role->givePermissionTo('delete university');
        $university_admin_role->givePermissionTo('store career');
        $university_admin_role->givePermissionTo('delete career');
        $university_admin_role->givePermissionTo('manage career admins');

        $career_admin_role = Role::query()->create([
            'name' => 'career_admin',
            'guard_name' => 'api',
        ]);
        $career_admin_role->givePermissionTo('store career');
        $career_admin_role->givePermissionTo('delete career');
        $career_admin_role->givePermissionTo('store exam');
        $career_admin_role->givePermissionTo('manage subject admins');

        $subject_admin_role = Role::query()->create([
            'name' => 'subject_admin',
            'guard_name' => 'api',
        ]);
        $subject_admin_role->givePermissionTo('store exam');

        $student_role = Role::query()->create([
            'name' => 'student',
            'guard_name' => 'api',
        ]);
        $student_role->givePermissionTo('store exam');
        $student_role->givePermissionTo('vote comments');
    }
}
