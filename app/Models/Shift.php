<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(\App\Observers\ShiftObserver::class)]
class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'attention_profile_id',
        'room_id',
        'state',
        'module_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function attentionProfile()
    {
        return $this->belongsTo(AttentionProfile::class, 'attention_profile_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function qualification()
    {
        return $this->hasOne(Qualification::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'shift_has_service');
    }

    // public function getStateAttribute()
    // {
    //     return $this->state_new;
    // }

    // protected $hidden = ['state_new'];

    // public function setStateAttribute($value)
    // {
    //     $this->state_new = $value;
    // }
}
