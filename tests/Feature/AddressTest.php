<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AddressTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.$contact->id.'/addresses',[
            'street'=> 'test',
            'city'=> 'test',
            'province'=> 'test',
            'country'=> 'test',
            'postal_code'=> '123456'
        ],
        [
            'Authorization'=> 'token-user1'
        ])->assertStatus(201)->assertJson([
            'data'=> [
                'street'=> 'test',
                'city'=> 'test',
                'province'=> 'test',
                'country'=> 'test',
                'postal_code'=> '123456'
            ]
        ]);
    }

    public function testCreateFailed(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.$contact->id.'/addresses',[
            'street'=> 'test',
            'city'=> 'test',
            'province'=> 'test',
            'country'=> '',
            'postal_code'=> '123456'
        ],
        [
            'Authorization'=> 'token-user1'
        ])->assertStatus(400)->assertJson([
            'errors'=> [
                'country'=> [
                    'The country field is required.'
                ]
            ]
        ]);
    }

    public function testCreateContactNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.($contact->id +1).'/addresses',[
            'street'=> 'test',
            'city'=> 'test',
            'province'=> 'test',
            'country'=> 'test',
            'postal_code'=> '123456'
        ],
        [
            'Authorization'=> 'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=> [
                'message'=> ['not found']
            ]
        ]);
    }

    public function testGetSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id,[
            'Authorization'=> 'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>[
                'street'=> 'test',
                'city'=> 'test',
                'province'=> 'test',
                'country'=> 'test',
                'postal_code'=> '123456'
            ]
        ]);
    }

    public function testGetNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1),[
            'Authorization'=> 'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=>[
                'message'=> ['not found']
            ]
        ]);
    }

    public function testUpdateSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id,[
            'street'=> 'update',
            'city'=> 'update',
            'province'=> 'update',
            'country'=> 'update',
            'postal_code'=> '123456'
        ],[
            'Authorization'=> 'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>[
                'street'=> 'update',
                'city'=> 'update',
                'province'=> 'update',
                'country'=> 'update',
                'postal_code'=> '123456'
             ]
        ]);
    }

    public function testUpdateFailed(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id,[
            'street'=> 'update',
            'city'=> 'update',
            'province'=> 'update',
            'country'=> '',
            'postal_code'=> '123456'
        ],[
            'Authorization'=> 'token-user1'
        ])->assertStatus(400)->assertJson([
            'errors'=>[
                'country'=> ['The country field is required.'],
             ]
        ]);
    }

    public function testUpdateNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1),[
            'street'=> 'update',
            'city'=> 'update',
            'province'=> 'update',
            'country'=> 'update',
            'postal_code'=> '123456'
        ],[
            'Authorization'=> 'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=>[
                'message'=> ['not found']
            ]
        ]);
    }

    public function testDeleteSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contacts/'.$address->contact_id.'/addresses/'.$address->id,[
            
        ],[
            'Authorization'=> 'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>true
        ]);
    }

    public function testDeleteNotFound(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contacts/'.$address->contact_id.'/addresses/'.($address->id+1),[
            
        ],[
            'Authorization'=> 'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors'=>[
                'message'=> ['not found']
            ]
        ]);
    }

    public function testListSuccess(){
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id.'/addresses',[
            'Authorization'=> 'token-user1'
        ])->assertStatus(200)->assertJson([
            'data'=>[[
                'street'=> 'test',
                'city'=> 'test',
                'province'=> 'test',
                'country'=> 'test',
                'postal_code'=> '123456'
            ]  
            ]
        ]);
    }

    public function testListContactNotFound()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1) . '/addresses', [
            'Authorization' => 'token-user1'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => ['not found']
            ]
        ]);
    }

    public function testGetOtherUserAddress()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'Authorization' => 'token-user2' // User 2 mencoba intip alamat punya User 1
        ])->assertStatus(403);
    }

    public function testUpdateOtherUserAddress()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [
            'street' => 'update',
            'city' => 'update',
            'province' => 'update',
            'country' => 'update',
            'postal_code' => '123456'
        ], [
            'Authorization' => 'token-user2'
        ])->assertStatus(403);
    }

    public function testDeleteOtherUserAddress()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [], [
            'Authorization' => 'token-user2'
        ])->assertStatus(403);
    }

    public function testListOtherUserAddress()
    {
        $this->seed([RoleAndPermissionSeeder::class, UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses', [
            'Authorization' => 'token-user2'
        ])->assertStatus(403);
    }
}
