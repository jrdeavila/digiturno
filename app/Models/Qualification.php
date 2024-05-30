<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'qualification',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
