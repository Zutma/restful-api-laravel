<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    
    public function testCreateCommentSuccess(){
        $this->seed([UserSeeder::class]);


        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'test'
        ])->assertStatus(201)
        ->assertJson([
            'data'=>[
                'content'=>'test',
            ]
        ]);
    }

    public function testGetListCommentsSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $this->get('/api/tasks/'.$task->id.'/comments',[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[[
                'content'=>'test',  
            ]]
        ]);
    }

    public function testDeleteCommentsSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $comment = Comment::query()->limit(1)->first();

        $this->delete('/api/tasks/'.$task->id.'/comments/'.$comment->id,[],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }
}
