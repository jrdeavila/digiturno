<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttentionProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'room_id',
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'attention_profile_service', 'attention_profile_id', 'service_id');
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
