<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;


class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'task-list']);
        Permission::create(['name' => 'task-create']);
        Permission::create(['name' => 'task-edit']);
        Permission::create(['name' => 'task-delete']);

        Permission::create(['name' => 'tag-manage']);
        Permission::create(['name' => 'comment-manage']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $supervisor =Role::create(['name' => 'supervisor']);
        $supervisor->givePermissionTo(['task-list','task-create','task-edit','task-delete']);

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo(['task-list']);
    }
}
