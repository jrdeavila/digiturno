<?php

namespace App\Models;

use App\Enums\ModuleStatus;
use App\Jobs\ModuleOffline;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(\App\Observers\ModuleObserver::class)]
class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'room_id',
        'client_type_id',
        'enabled',
        'module_type_id',
        'status',
        'responsable_id',
    ];

    // Agregar atributos
    protected $appends = [
        'current_shifts_count',
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

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function pendingShifts(): HasMany
    {
        return $this->shifts()->pending();
    }

    public function inProgressShifts(): HasMany
    {
        return $this->shifts()->inProgress();
    }

    public function completedShifts(): HasMany
    {
        return $this->shifts()->completed();
    }

    public function distractedShifts(): HasMany
    {
        return $this->shifts()->distracted();
    }

    public function currentShifts(): HasMany
    {
        return $this->shifts()->current();
    }

    public function getCurrentShiftsCountAttribute(): int
    {
        return $this->currentShifts()->count();
    }

    public function setOnline(): void
    {
        $this->status = 'online';
        $this->save();
    }

    // Si antes de las {MODULE_OFFLINE_CLOCK_TIME} del medio dia entonces se programara para una hora despues de la hora definida {MODULE_OFFLINE_CLOCK_TIME}
    public function programAutoOff(): void
    {
        $job = new ModuleOffline($this);
        $currentHour = now()->hour;
        $time1 = env("MODULE_OFFLINE_CLOCK_TIME_1");
        if ($currentHour < $time1) {
            $delay =  ($time1 + 1 - $currentHour);
        }
        $time2 = env("MODULE_OFFLINE_CLOCK_TIME_2");
        if ($currentHour >= $time1 && $currentHour < $time2) {
            $delay =  ($time2 + 1 - $currentHour);
        }
        $date = new Carbon();
        $date->addHours($delay);
        $date->setMinutes(0);
        dispatch($job->delay($date));
    }

    public function resetStatus(): void
    {
        if ($this->status === ModuleStatus::Offline->value) {
            $this->programAutoOff();
            $this->setOnline();
        }
    }
}
