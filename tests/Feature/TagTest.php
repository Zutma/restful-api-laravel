<?php

namespace Tests\Feature;

use App\Models\Tag;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;
    
    public function testTagCreateSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(201)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testGetListTagsSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $this->get('/api/tags',[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[[
                'name'=>'test'
            ]]
        ]);
    }

    public function testGetTagSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->get('/api/tags/'.$tag->id,[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testUpdateTagSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->put('/api/tags/'.$tag->id,[
            'name'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testDeleteTagSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->delete('/api/tags/'.$tag->id,[],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }
}
