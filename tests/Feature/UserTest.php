<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterSuccess(){
        $this->seed([RoleAndPermissionSeeder::class]);
        $this->post('api/users',[
            'username' => 'khannedyu',
            'password'=> 'rahasia',
            'name'=> 'eko',
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'username' => 'khannedyu',
                'name' => 'eko',
            ]
        ]); 
    }

    public function testRegisterFailed(){
        $this->post('api/users',[
            'username' => '',
            'password'=> '',
            'name'=> '',
        ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => ['The username field is required.'],
                'password' => ['The password field is required.'],
                'name' => ['The name field is required.'],
            ]
        ]); 
    }

    public function testRegisterUsernameAlreadyExist(){
        $this->seed([RoleAndPermissionSeeder::class]);
        $this->testRegisterSuccess();
        $this->post('api/users',[
            'username' => 'khannedyu',
            'password'=> 'rahasia',
            'name'=> 'eko',
        ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                'username' => ['username already registered'],
            ]
        ]); 
    }

    public function testLoginSuccess(){
       $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
       $this->post('api/users/login',[
            'username' => 'admin',
            'password'=> 'password',
       ])->assertStatus(200)
       ->assertJson([
            'data' => [
                'username' => 'admin',
                'name' => 'admin',
            ]
       ]);
       $user = User::where('username', 'admin')->first();
       $this->assertNotNull($user->token);
    } 

    public function testLoginFailedUsernameNotFound(){
        $this->post('api/users/login',[
            'username' => 'admin',
            'password'=> 'password',
       ])->assertStatus(401)
       ->assertJson([
            'errors' => [
                'message' => ['username or password wrong'],
            ]
       ]);
    }

    public function testLoginFailedPasswordWrong(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $this->post('api/users/login',[
            'username' => 'admin',
            'password'=> 'salah',
       ])->assertStatus(401)
       ->assertJson([
            'errors' => [
                'message' => ['username or password wrong'],
            ]
       ]);
    }

    public function testGetSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->get('/api/users/current',[
            'Authorization'=>'token-admin'
        ])->assertStatus(200)
            ->assertJson([
                'data'=>[
                    'username'=>'admin',
                    'name'=>'admin'
                ]
            ]);
    }

    public function testGetUnauthorized(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors'=>[
                    'message'=>[
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetInvalidToken(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->get('/api/users/current',[
            'Authorization'=>'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors'=>[
                    'message'=>[
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $oldUser = User::where('username','admin')->first();

        $this->patch('/api/users/current', [
            'name' => 'eko'
        ], [
            'Authorization' => 'token-admin'
        ])->assertStatus(200)
            ->assertJson([
                'data'=>[
                    'username'=>'admin',
                    'name'=>'eko'
                ]
            ]);

        $newUser = User::where('username','admin')->first();
        self::assertNotEquals($oldUser->name,$newUser->name);
    }

    public function testUpdatePasswordSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);
        $oldUser = User::where('username','admin')->first();

        $this->patch('/api/users/current', [
            'password' => 'baru'
        ], [
            'Authorization' => 'token-admin'
        ])->assertStatus(200)
            ->assertJson([
                'data'=>[
                    'username'=>'admin',
                    'name'=>'admin'
                ]
            ]);

        $newUser = User::where('username','admin')->first();
        self::assertNotEquals($oldUser->password,$newUser->password);
    }

    public function testUpdateFailed(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->patch('/api/users/current', [
            'name' => 'ekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoekoeko'
        ], [
            'Authorization' => 'token-admin'
        ])->assertStatus(400)
            ->assertJson([
                'errors'=>[
                    'name'=>[
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->delete(uri: '/api/users/logout',headers:[
            'Authorization'=> 'token-admin'
        ])->assertStatus(200)
            ->assertJson([
                "data"=>true
            ]);

        $user =User::where('username','admin')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers:[
            'Authorization'=> 'salah'
        ])->assertStatus(401)
            ->assertJson([
                "errors"=>[
                    "message"=>[
                        "unauthorized"
                    ]
                ]
            ]);
    }


}
