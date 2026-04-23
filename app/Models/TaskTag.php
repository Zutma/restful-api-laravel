<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTag extends Model
{
    protected $table = "task_tags";
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'task_id',
        'tag_id'
    ];

    public function task(): BelongsTo{
        return $this->belongsTo(Task::class,"task_id","id");
    }

    public function tag(): BelongsTo{
        return $this->belongsTo(Tag::class,"tag_id","id");
    }
}
