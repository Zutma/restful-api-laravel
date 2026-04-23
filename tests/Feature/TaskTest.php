<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ])->assertStatus(201)
        ->assertJson([
            'data'=>[
                'title'=>'test',
                'description'=>'test',
                'status'=>false,
            ]
        ]);
    }

    public function testGetTaskListSuccess(){
        $this->seed([UserSeeder::class]);
        
        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $this->get('/api/tasks',[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[[
                'title'=>'test',
                'description'=>'test',
                'status'=>false,
            ]]
        ]);
    }

    public function testGetTaskSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->get('/api/tasks/'.$task->id,[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'title'=>'test',
                'description'=>'test',
                'status'=>false,
            ]
        ]);
    }

    public function testUpdateSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->put('/api/tasks/'.$task->id,[
            'title'=>'test',
            'description'=>'test',
            'status'=>true,
        ],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>[
                'title'=>'test',
                'description'=>'test',
                'status'=>true,
            ]
        ]);
    }

    public function testTaskDeleteSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->delete('/api/tasks/'.$task->id,[],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testAttachTagSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $this->post('/api/tags',[ 
            'name'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();
        $tag = Tag::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/tags',[ 
            'tag_id'=>$tag->id,
        ],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testDetachTagSuccess(){
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $this->post('/api/tags',[ 
            'name'=>'test',
        ],[
            'Authorization'=>'test'
        ]);

        $task = Task::query()->limit(1)->first();
        $tag = Tag::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/tags',[ 
            'tag_id'=>$tag->id,
        ],[
            'Authorization'=>'test'
        ]);

        $this->delete('/api/tasks/'.$task->id.'/tags/'.$tag->id,[],[
            'Authorization'=>'test'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testGetTaskNotFound()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/tasks/9999', [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['not found']
                ]
            ]);
    }

    public function testGetTaskUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/tasks/1', [
            'Authorization' => 'salah_token'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['unauthorized']
                ]
            ]);
    }

    public function testGetTaskOtherUser()
    {
        $this->seed([UserSeeder::class]);
        // 1. Buat Task sebagai User 'test'
        $this->post('/api/tasks', ['title' => 'rahasia'], ['Authorization' => 'test']);
        $task = Task::query()->first();

        // 2. Buat User baru (User B) secara manual di database
        User::create([
            'username' => 'orang_asing',
            'password' => Hash::make('password'),
            'name' => 'orang asing',
            'token' => 'token_asing'
        ]);

        // 3. Coba intip pakai token 'token_asing'
        $this->get('/api/tasks/' . $task->id, [
            'Authorization' => 'token_asing'
        ])->assertStatus(404); // Harus 404 karena dia nggak berhak lihat
    }

    public function testDeleteTaskOtherUser()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'milik_ku'], ['Authorization' => 'test']);
        $task = Task::query()->first();

        // Buat orang asing
        User::create([
            'username' => 'penyusup',
            'password' => Hash::make('password'),
            'name' => 'penyusup',
            'token' => 'token_penyusup'
        ]);

        // Si penyusup mencoba menghapus
        $this->delete('/api/tasks/' . $task->id, [], [
            'Authorization' => 'token_penyusup'
        ])->assertStatus(404); // Harus gagal (404) karena bukan miliknya
    }

    public function testCreateTaskValidationError()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/tasks', [
            'title' => '', // Sengaja dikosongkan
            'description' => 'test'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400) // Harus balik 400
            ->assertJson([
                'errors' => [
                    'title' => [
                        'The title field is required.'
                    ]
                ]
            ]);
    }

    public function testAttachAssigneeSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'kerjasama'], ['Authorization' => 'test']);
        $task = Task::query()->first();

        $user2 = User::where('username', 'test2')->first();

        $this->post('/api/tasks/' . $task->id . '/assignees', [
            'user_id' => $user2->id
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDetachAssigneeSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'kerjasama'], ['Authorization' => 'test']);
        $task = Task::query()->first();

        $user2 = User::where('username', 'test2')->first();

        // Pasang dulu
        $task->assignees()->attach($user2->id);

        // Lalu hapus
        $this->delete('/api/tasks/' . $task->id . '/assignees/' . $user2->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
}

