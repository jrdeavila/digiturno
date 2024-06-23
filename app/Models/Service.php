<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'service_id'
    ];

    public function subservices()
    {
        return $this->hasMany(Service::class, 'service_id',  'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function attentionProfiles()
    {
        return $this->belongsToMany(AttentionProfile::class, 'attention_profile_service', 'service_id', 'attention_profile_id');
    }
}
