<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AddressPolicy
{
    public function view(User $user, Address $address): bool
    {
        return $user->can('address-manage') || $address->contact->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Address $address): bool
    {
        return $user->can('address-manage') || $address->contact->user_id === $user->id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $user->can('address-manage') || $address->contact->user_id === $user->id;
    }
}
