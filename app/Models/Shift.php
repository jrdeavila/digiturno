<?php

namespace App\Models;

use App\Enums\ShiftState;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

    protected $with = [
        'attentionProfile',
        'room',
        'client',
        'module',
    ];

    public $appends = [
        'in_progress_started_at',
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

    public function histories()
    {
        return $this->hasMany(ShiftHistory::class);
    }

    public function scopePending($query): Builder
    {
        return $query->where('state', ShiftState::Pending)->orWhere('state', ShiftState::PendingTransferred);
    }

    public function scopeQualified($query): Builder
    {
        return $query->where('state', ShiftState::Qualified);
    }

    public function scopeCompleted($query): Builder
    {
        return $query->where('state', ShiftState::Completed);
    }


    public function scopeCancelled($query): Builder
    {
        return $query->where('state', ShiftState::Cancelled);
    }

    public function scopeInProgress($query): Builder
    {
        return $query->where('state', ShiftState::InProgress);
    }

    public function scopeDistracted($query): Builder
    {
        return $query->where('state', ShiftState::Distracted);
    }

    public function scopeCurrent($query): Builder
    {
        return $query->whereIn('state', array_map(fn($state) => $state->value, [
            ShiftState::Pending,
            ShiftState::PendingTransferred,
            ShiftState::InProgress,
            ShiftState::Called,
        ]));
    }

    public function scopeToDay($query): Builder
    {
        return $query->whereDate('created_at', now()->format('Y-m-d'));
    }

    public function getInProgressStartedAtAttribute(): ?string
    {
        // Last in progress
        $inProgress = $this->histories()->inProgress()->latest()->first();

        return $inProgress ? $inProgress->created_at->setTimezone('America/Bogota')->format('Y-m-d h:i A') : null;
    }
}
