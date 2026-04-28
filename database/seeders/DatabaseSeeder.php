<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(AddressSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(TaskAssigneeSeeder::class);
        $this->call(TaskTagSeeder::class);
    }
}
