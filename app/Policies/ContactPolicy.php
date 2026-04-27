<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function view(User $user, Contact $contact): bool
    {
        return $user->role === 'admin' || $contact->user_id === $user->id;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->role === 'admin' || $contact->user_id === $user->id;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->role === 'admin' || $contact->user_id === $user->id;
    }
}
