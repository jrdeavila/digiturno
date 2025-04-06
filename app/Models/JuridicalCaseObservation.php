<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuridicalCaseObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'juridical_case_id',
        'attendant_id',
    ];

    public function juridicalCase()
    {
        return $this->belongsTo(JuridicalCase::class);
    }

    public function attendant()
    {
        return $this->belongsTo(Attendant::class);
    }
}
