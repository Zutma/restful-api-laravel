<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignee extends Model
{
    protected $table = "task_assignees";
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'task_id',
        'user_id'
    ];

    public function task(): BelongsTo{
        return $this->belongsTo(Task::class,"task_id","id");
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class,"user_id","id");
    }

}
