<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    protected $table = "tasks";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $attributes = [
        'status' => false,
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class,"user_id","id");
    }

    public function comments(): MorphMany{
        return $this->morphMany(Comment::class,"commentable");
    }

    public function tags(): BelongsToMany{
        return $this->belongsToMany(Tag::class,"task_tags","task_id","tag_id");
    }

    public function assignees(): BelongsToMany{
        return $this->belongsToMany(User::class,"task_assignees","task_id","user_id");
    }
}
