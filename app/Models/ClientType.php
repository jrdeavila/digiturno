<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'priority'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getTypeAttribute($value)
    {
        switch ($value) {
            case "standard":
                return 'Estándar';
            case "preferential":
                return 'Preferencial';
            case "processor":
                return 'Tramitador';
            default:
                return 'Sin tipo';
        }
    }
}
