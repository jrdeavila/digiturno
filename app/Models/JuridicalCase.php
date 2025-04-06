<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuridicalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'client_id',
        'attendant_id',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function attendant()
    {
        return $this->belongsTo(Attendant::class);
    }

    public function observations()
    {
        return $this->hasMany(JuridicalCaseObservation::class);
    }
}
