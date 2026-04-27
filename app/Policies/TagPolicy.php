<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function view(User $user, Tag $tag): bool
    {
        return $user->role === 'admin' || $tag->user_id === $user->id;
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->role === 'admin' || $tag->user_id === $user->id;
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->role === 'admin' || $tag->user_id === $user->id;
    }
}
