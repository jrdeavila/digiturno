<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'qualification',
        'shift_module_assignation_id',
    ];

    public function shiftModuleAssignation()
    {
        return $this->belongsTo(ShiftModuleAssignation::class);
    }

    public function shift()
    {
        return $this->shiftModuleAssignation->shift;
    }
}
