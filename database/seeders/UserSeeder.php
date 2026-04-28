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
            'username'=> 'test',
            'password'=> Hash::make('test'),    
            'name'=>'test',
            'token' => 'test',
        ])->assignRole('admin');

        User::create([
            'username'=> 'test2',
            'password'=> Hash::make('test2'),    
            'name'=>'test2',
            'token' => 'test2'
        ])->assignRole('supervisor');

        User::create([
            'username'=> 'test3',
            'password'=> Hash::make('test3'),    
            'name'=>'test3',
            'token' => 'test3'
        ])->assignRole('user');
    }
}
