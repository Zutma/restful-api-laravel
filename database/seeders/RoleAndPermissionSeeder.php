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

        Permission::firstOrCreate(['name' => 'task-list']);
        Permission::firstOrCreate(['name' => 'task-manage']);
        Permission::firstOrCreate(['name' => 'tag-manage']);
        Permission::firstOrCreate(['name' => 'comment-manage']);
        Permission::firstOrCreate(['name' => 'contact-manage']);
        Permission::firstOrCreate(['name' => 'address-manage']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions(['task-list','contact-manage','address-manage','comment-manage','tag-manage']);

        $supervisor =Role::firstOrCreate(['name' => 'supervisor']);
        $supervisor->syncPermissions(['task-manage', 'tag-manage', 'comment-manage']);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([]);
    }
}
