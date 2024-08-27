<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(\App\Observers\AttendantObserver::class)]
class Attendant extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'dni',
        'password',
        'enabled',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_attendant_accesses')->withTimestamps();
    }

    public function shifts()
    {
        return $this->modules()->wherePivot('created_at', '>=', now()->startOfDay())->first()?->shifts;
    }

    public function haveShiftInProgress()
    {
        $shits = $this->shifts()->where('state', \App\Enums\ShiftState::InProgress);
        return $shits->isNotEmpty();
    }

    public function haveShiftCompleted()
    {
        $shifts =  $this->shifts()->where('state', \App\Enums\ShiftState::Completed);
        return $shifts->isNotEmpty();
    }


    public function absences()
    {
        return $this->belongsToMany(
            AbsenceReason::class,
            'attendant_absence_reason',
            'attendant_id',
            'absence_reason_id',
        )->withTimestamps();
    }

    public function currentModule(): ?\App\Models\Module
    {
        return $this->modules()->whereDate(
            'module_attendant_accesses.created_at',
            now()->toDateString()
        )->first();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
