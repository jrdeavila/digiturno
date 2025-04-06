<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;


#[ObservedBy(\App\Observers\ModuleObserver::class)]
class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'room_id',
        'client_type_id',
        'attention_profile_id',
        'enabled',
        'module_type_id',
        'status',
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

    public function currentAttendant(): ?\App\Models\Attendant
    {
        return $this->attendants()->whereDate(
            'module_attendant_accesses.created_at',
            now()->toDateString()
        )
            // Order by the last attendant that accessed the module
            ->orderBy('module_attendant_accesses.created_at', 'desc')
            ->first();
    }

    public function attentionProfile()
    {
        return $this->belongsTo(AttentionProfile::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function moduleType()
    {
        return $this->belongsTo(ModuleType::class);
    }
}
