<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        
        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $this->get('/api/tasks',[
            'Authorization'=>'token-supervisor1'
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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->get('/api/tasks/'.$task->id,[
            'Authorization'=>'token-supervisor1'
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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->put('/api/tasks/'.$task->id,[
            'title'=>'test',
            'description'=>'test',
            'status'=>true,
        ],[
            'Authorization'=>'token-supervisor1'
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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();

        $this->delete('/api/tasks/'.$task->id,[],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testAttachTagSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $this->post('/api/tags',[ 
            'name'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();
        $tag = Tag::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/tags',[ 
            'tag_id'=>$tag->id,
        ],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testDetachTagSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks',[ 
            'title'=>'test',
            'description'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $this->post('/api/tags',[ 
            'name'=>'test',
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $task = Task::query()->limit(1)->first();
        $tag = Tag::query()->limit(1)->first();

        $this->post('/api/tasks/'.$task->id.'/tags',[ 
            'tag_id'=>$tag->id,
        ],[
            'Authorization'=>'token-supervisor1'
        ]);

        $this->delete('/api/tasks/'.$task->id.'/tags/'.$tag->id,[],[
            'Authorization'=>'token-supervisor1'
        ])->assertStatus(200)
        ->assertJson([
            'data'=>true
        ]);
    }

    public function testGetTaskNotFound()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->get('/api/tasks/9999', [
            'Authorization' => 'token-supervisor1'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['not found']
                ]
            ]);
    }

    public function testGetTaskUnauthorized()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        // 1. Buat Task sebagai User 'supervisor1'
        $this->post('/api/tasks', ['title' => 'rahasia'], ['Authorization' => 'token-supervisor1']);
        $task = Task::query()->first();

        // 2. Buat User baru (SPV 2) secara manual di database
        $spv2 = User::create([
            'username' => 'supervisor_2',
            'password' => Hash::make('password'),
            'name' => 'spv 2',
            'token' => 'token_spv2'
        ])->assignRole('supervisor');

        // 3. Coba intip pakai token 'token_spv2'
        $this->get('/api/tasks/' . $task->id, [
            'Authorization' => 'token_spv2'
        ])->assertStatus(404); // Harus 404 karena dia nggak berhak lihat milik SPV lain
    }

    public function testDeleteTaskOtherUser()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'milik_ku'], ['Authorization' => 'token-supervisor1']);
        $task = Task::query()->first();

        // Buat orang asing (SPV lain)
        $spv2 = User::create([
            'username' => 'supervisor_2',
            'password' => Hash::make('password'),
            'name' => 'spv 2',
            'token' => 'token_spv2'
        ])->assignRole('supervisor');

        // Si penyusup mencoba menghapus
        $this->delete('/api/tasks/' . $task->id, [], [
            'Authorization' => 'token_spv2'
        ])->assertStatus(403); // Harus 403 karena dia dilarang Policy
    }

    public function testCreateTaskValidationError()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/tasks', [
            'title' => '', // Sengaja dikosongkan
            'description' => 'test'
        ], [
            'Authorization' => 'token-supervisor1'
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
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'kerjasama'], ['Authorization' => 'token-supervisor1']);
        $task = Task::query()->first();

        $user1 = User::where('username', 'user1')->first();

        $this->post('/api/tasks/' . $task->id . '/assignees', [
            'user_id' => $user1->id
        ], [
            'Authorization' => 'token-supervisor1'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDetachAssigneeSuccess()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $this->post('/api/tasks', ['title' => 'kerjasama'], ['Authorization' => 'token-supervisor1']);
        $task = Task::query()->first();

        $user1 = User::where('username', 'user1')->first();

        // Pasang dulu
        $task->assignees()->attach($user1->id);

        // Lalu hapus
        $this->delete('/api/tasks/' . $task->id . '/assignees/' . $user1->id, [], [
            'Authorization' => 'token-supervisor1'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }
}

