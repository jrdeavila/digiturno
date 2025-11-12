<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftHistory extends Model
{
    protected $table = 'shift_histories';

    protected $fillable = [
        'shift_id',
        'state',
        'responsable_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
