<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $spv1 = User::where('username', 'supervisor1')->first();
        $spv2 = User::where('username', 'supervisor2')->first();
        $user1 = User::where('username', 'user1')->first();
        $user2 = User::where('username', 'user2')->first();

        $tag1 = Tag::create(['name' => 'Tag 1', 'user_id' => $spv1->id]);
        $tag2 = Tag::create(['name' => 'Tag 2', 'user_id' => $spv1->id]);

        // 2. Create Tasks for SPV 1
        $task1 = Task::create([
            'title' => 'Tugas 1',
            'description' => 'Deskripsi Tugas 1',
            'user_id' => $spv1->id
        ]);
        $task1->tags()->attach([$tag1->id, $tag2->id]);
        $task1->assignees()->attach([$user1->id, $user2->id]);

        $task2 = Task::create([
            'title' => 'Tugas 2',
            'description' => 'Deskripsi Tugas 2',
            'user_id' => $spv1->id
        ]);
        $task2->assignees()->attach([$user1->id]);

        $task2 = Task::create([
            'title' => 'Tugas 3',
            'description' => 'Deskripsi Tugas 3',
            'user_id' => $spv1->id
        ]);

        $task3 = Task::create([
            'title' => 'Tugas 4',
            'description' => 'Deskripsi Tugas 4',
            'user_id' => $spv2->id
        ]);

        $contact1 = Contact::create([
            'first_name' => 'First 1',
            'last_name' => 'Last 1',
            'email' => 'user1@example.com',
            'phone' => '0811111111',
            'user_id' => $user1->id
        ]);
        Address::create([
            'contact_id' => $contact1->id,
            'street' => 'Street 1',
            'city' => 'City 1',
            'province' => 'Province 1',
            'country' => 'Country 1',
            'postal_code' => '11111'
        ]);

        $contact2 = Contact::create([
            'first_name' => 'First 2',
            'last_name' => 'Last 2',
            'email' => 'user2@example.com',
            'phone' => '0822222222',
            'user_id' => $user2->id
        ]);

        $task1->comments()->create([
            'content' => 'Komentar 1',
            'user_id' => $user1->id
        ]);
        $task1->comments()->create([
            'content' => 'Komentar 2',
            'user_id' => $user2->id
        ]);
    }
}
