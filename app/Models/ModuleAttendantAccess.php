<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleAttendantAccess extends Model
{
    use HasFactory;

    protected $table = 'module_attendant_accesses';

    protected $fillable = [
        'module_id',
        'attendant_id',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function attendant()
    {
        return $this->belongsTo(Attendant::class);
    }
}
