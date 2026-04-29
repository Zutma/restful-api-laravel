<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    
    public function testCreateCommentSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();
        $user1 = User::where('username', 'user1')->first();
        $task->assignees()->attach($user1->id);

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(201)
        ->assertJson([
            'data'=>[
                'content'=>'test',
            ]
        ]);
    }

    public function testGetListCommentsSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();
        $user1 = User::where('username', 'user1')->first();
        $task->assignees()->attach($user1->id);

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'token-user1'
        ]);

        $this->get('/api/tasks/'.$task->id.'/comments',[
            'Authorization'=>'token-user1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[[
                'content'=>'test',  
            ]]
        ]);
    }

    public function testDeleteCommentsSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();
        $user1 = User::where('username', 'user1')->first();
        $task->assignees()->attach($user1->id);

        $this->post('/api/tasks/'.$task->id.'/comments',[
            'content'=>'test',
        ],[
            'Authorization'=>'token-user1'
        ]);

        $comment = Comment::query()->limit(1)->first();

        $this->delete('/api/tasks/'.$task->id.'/comments/'.$comment->id,[],[
            'Authorization'=>'token-user1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }
}
