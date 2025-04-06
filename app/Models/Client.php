<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(\App\Observers\ClientObserver::class)]
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'dni',
        'client_type_id',
    ];

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
