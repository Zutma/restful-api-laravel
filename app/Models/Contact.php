<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contact extends Model
{
    use LogsActivity;

    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $table= 'contacts';
    public $incrementing = true;
    public $timestamps = true;

    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()
            ->logOnly(['first_name','last_name','email','phone'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Contact has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'user_id'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class,"user_id","id");
    }

    public function addresses(): HasMany{
        return $this->hasMany(Address::class,"contact_id","id");
    }
}

