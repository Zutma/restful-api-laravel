<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $table = "tags"; 
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name'
    ];

    public function tasks(): BelongsToMany{
        return $this->belongsToMany(Task::class,"task_tags","tag_id","task_id");
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class,"user_id","id");
    }
}
