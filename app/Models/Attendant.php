<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'dni',
        'password',
        'enabled'
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

    public function absences()
    {
        return $this->belongsToMany(
            AbsenceReason::class,
            'attendant_absence_reason',
            'attendant_id',
            'absence_reason_id',
        )->withTimestamps();
    }
}
