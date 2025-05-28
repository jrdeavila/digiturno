<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = "cargos";
    protected $connection = "timeit";

    protected $primaryKey = 'id';
    public $timestamps = false;

    public $appends = [
        'name',
        'area_id',
    ];

    public $hidden = [
        'created_at',
        'updated_at',
        'nombre',
        'Areas_id',
    ];

    public function getAreaIdAttribute()
    {
        return $this->attributes['Areas_id'];
    }

    public function setAreaIdAttribute($value)
    {
        $this->attributes['Areas_id'] = $value;
    }

    public function getNameAttribute()
    {
        return $this->attributes['nombre'];
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }
}
