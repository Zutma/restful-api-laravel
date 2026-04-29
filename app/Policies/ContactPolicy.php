<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function view(User $user, Contact $contact): bool
    {
        return $user->can('contact-manage') || $contact->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $user->can('contact-manage') || $contact->user_id === $user->id;
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $user->can('contact-manage') || $contact->user_id === $user->id;
    }
}
