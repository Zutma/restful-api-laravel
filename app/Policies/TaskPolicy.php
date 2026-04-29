<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->can('task-list') || $task->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('task-manage');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->can('task-manage') && $task->user_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        // return $user->role === 'admin' || $user->id === $task->user_id;
        return $user->can('task-manage') && $task->user_id === $user->id;
    }    
}

































 //$task->assignees()->where('user_id', $user->id)->exists();