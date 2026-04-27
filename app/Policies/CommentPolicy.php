<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function view(User $user, Comment $comment): bool
    {
        // Comments are usually public if you can see the task, 
        // but for RBAC consistency, we'll allow admin or owner.
        return $user->role === 'admin' || $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' || $comment->user_id === $user->id;
    }
}
