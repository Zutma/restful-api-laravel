<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess(){

        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/contacts',[
            'first_name'=> 'eko',
            'last_name'=>'khannedy',
            'email'=> 'eko@pzn.com',
            'phone'=>'0123345789'
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(201)->assertJson([
            'data'=>[
                'first_name'=> 'eko',
                'last_name'=>'khannedy',
                'email'=> 'eko@pzn.com',
                'phone'=>'0123345789'
                ]
            ]); 
    }

    public function testCreateFailed(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/contacts',[
            'first_name'=> '',
            'last_name'=>'khannedy',
            'email'=> 'ekopzn.com',
            'phone'=>'0123345789'
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(400)->assertJson([
            'errors'=>[
                'first_name'=>[
                    'The first name field is required.'
                ],
                'email'=>[
                    'The email field must be a valid email address.'
                ]
                ]
            ]);
    }

    public function testCreateUnauthorized(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class]);

        $this->post('/api/contacts',[
            'first_name'=> '',
            'last_name'=>'khannedy',
            'email'=> 'ekopzn.com',
            'phone'=>'0123345789'
        ],[
            'Authorization'=>'salah'
        ])->assertStatus(401)->assertJson([
            'errors'=>[
                'message'=> ['unauthorized']
                ]
            ]);
    }

    public function testGetSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id,[ 
            'Authorization'=>'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>[
                'first_name'=> 'test',
                'last_name'=>'test',
                'email'=> 'test@pzn.com',
                'phone'=>'0123345789'
                ]
            ]);
    }

    public function testGetNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.($contact->id+1),[ 
            'Authorization'=>'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=>[
                'message'=> ['not found']
                ]
            ]);
    }

    public function testGetOtherUserContact(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id,[ 
            'Authorization'=>'token-user2'
        ])->assertStatus(403);
    }

    public function testUpdateSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/'.$contact->id,[ 
            'first_name'=> 'test2',
            'last_name'=>'test2',
            'email'=> 'test2@pzn.com',
            'phone'=>'0123345789'
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>[
                'first_name'=> 'test2',
                'last_name'=>'test2',
                'email'=> 'test2@pzn.com',
                'phone'=>'0123345789'
                ]
            ]);

    }

    public function testUpdateValidationError(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/'.$contact->id,[ 
            'first_name'=> '',
            'last_name'=>'test2',
            'email'=> 'test2@pzn.com',
            'phone'=>'0123345789'
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(400)->assertJson([
            'errors'=>[
                'first_name'=> ['The first name field is required.']
                ]
            ]);
    }

    public function testDeleteSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/'.$contact->id,[ 
            
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>true
            ]);
    }

    public function testDeleteNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/'.($contact->id+1),[ 
            
        ],[
            'Authorization'=>'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=>[
                'message'=> ['not found']
                ]
            ]);
    }

    public function testSearchByFirstName(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=first',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=last',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail(){
       $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?email=test',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']); 
    }

    public function testSearchByPhone(){
       $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?phone=11111',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']); 
    }

    public function testSearchNotFound(){
       $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=salah',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']); 
    }

    public function testSearchByPage(){
      $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?size=5&page=2',[
            'Authorization'=>'token-user1'
        ])
        ->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);   
        self::assertEquals(2, $response['meta']['current_page']);
    }

    public function testUpdateOtherUserContact()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test2@pzn.com',
            'phone' => '0123345789'
        ], [
            'Authorization' => 'token-user2'
        ])->assertStatus(403);
    }

    public function testDeleteOtherUserContact()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'token-user2'
        ])->assertStatus(403);
    }
}
