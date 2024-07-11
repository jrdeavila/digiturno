<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftModuleAssignation extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'module_id',
        'status'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function qualifications()
    {
        return $this->hasMany(Qualification::class);
    }
}
