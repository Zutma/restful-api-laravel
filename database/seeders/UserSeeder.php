<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username'=> 'admin',
            'password'=> Hash::make('password'),    
            'name'=>'admin',
            'token' => 'token-admin',
        ])->assignRole('admin');

        User::create([
            'username'=> 'manager',
            'password'=> Hash::make('password'),    
            'name'=>'manager',
            'token' => 'token-manager'
        ])->assignRole('manager');

        User::create([
            'username'=> 'supervisor1',
            'password'=> Hash::make('password'),    
            'name'=>'supervisor1',
            'token' => 'token-supervisor1'
        ])->assignRole('supervisor');

        User::create([
            'username'=> 'supervisor2',
            'password'=> Hash::make('password'),    
            'name'=>'supervisor2',
            'token' => 'token-supervisor2'
        ])->assignRole('supervisor');

        User::create([
            'username'=> 'user1',
            'password'=> Hash::make('password'),    
            'name'=>'user1',
            'token' => 'token-user1'
        ])->assignRole('user');

        User::create([
            'username'=> 'user2',
            'password'=> Hash::make('password'),    
            'name'=>'user2',
            'token' => 'token-user2'
        ])->assignRole('user');
    }
}
