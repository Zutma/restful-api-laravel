<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tag extends Model
{
    use LogsActivity;

    protected $table = "tags"; 
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Tag has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

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
