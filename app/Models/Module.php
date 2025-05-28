<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'user_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function clientType(): BelongsTo
    {
        return $this->belongsTo(ClientType::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id', 'id');
    }

    public function attentionProfile(): BelongsTo
    {
        return $this->belongsTo(AttentionProfile::class);
    }

    public function attentionProfiles(): BelongsToMany
    {
        return $this->belongsToMany(AttentionProfile::class, 'module_has_attention_profiles');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function moduleType(): BelongsTo
    {
        return $this->belongsTo(ModuleType::class);
    }
}
