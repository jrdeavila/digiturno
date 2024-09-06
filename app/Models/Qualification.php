<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'qualification',
        'shift_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getQualificationAttribute($value)
    {
        switch ($value) {
            case "bad":
                return 'Malo';
            case "regular":
                return 'Regular';
            case "good":
                return 'Bueno';
            case "excellent":
                return 'Excelente';
            default:
                return 'Sin calificar';
        }
    }
}
