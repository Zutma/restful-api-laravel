<?php

namespace Tests\Feature;

use App\Models\Tag;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;
    
    public function testTagCreateSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test',
        ],[
            'Authorization'=>'test'
        ])->assertStatus(201)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testGetListTagsSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'test'
        ]);

        $this->get('/api/tags',[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[[
                'name'=>'test'
            ]]
        ]);
    }

    public function testGetTagSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'test'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->get('/api/tags/'.$tag->id,[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testUpdateTagSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'test'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->put('/api/tags/'.$tag->id,[
            'name'=>'test',
        ],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'name'=>'test'
            ]
        ]);
    }

    public function testDeleteTagSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tags',[
            'name'=>'test'
        ],[
            'Authorization'=>'test'
        ]);

        $tag = Tag::query()->limit(1)->first();

        $this->delete('/api/tags/'.$tag->id,[],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }
}
