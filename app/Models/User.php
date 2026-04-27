<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{

    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'username',
        'password',
        'name',
        'role',
        'token'
    ];

    protected $attributes = [
        'role'=> 'user'
    ];

    public function contacts(): HasMany{
        return $this->hasMany(Contact::class,"user_id","id");
    }

    public function tasks(): HasMany{
        return $this->hasMany(Task::class,"user_id","id");
    }

    public function assignedTasks(): BelongsToMany{
        return $this->belongsToMany(Task::class, "task_assignees", "user_id", "task_id");
    }

    public function comments(): HasMany{
        return $this->hasMany(Comment::class, "user_id", "id");
    }
}