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
}
