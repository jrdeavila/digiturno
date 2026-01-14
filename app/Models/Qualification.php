<?php

namespace App\Models;

use App\Observers\QualificationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(QualificationObserver::class)]
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
