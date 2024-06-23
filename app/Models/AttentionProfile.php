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
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'attention_profile_service', 'attention_profile_id', 'service_id');
    }
}
