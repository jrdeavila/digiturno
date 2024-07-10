<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'room_id',
        'client_type_id',
        'attention_profile_id',
        'enabled'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function attendants()
    {
        return $this->belongsToMany(Attendant::class, 'module_attendant_accesses')->withTimestamps();
    }

    public function attentionProfile()
    {
        return $this->belongsTo(AttentionProfile::class);
    }
}
