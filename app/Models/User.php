<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = "usuarios";
    protected $connection = "timeit";

    protected $primaryKey = "id";

    public $timestamps = false;


    protected $appends = ['role', 'status', 'email', 'employee_id'];

    protected $hidden = [
        'clave',
        'correo',
        'rol',
        'estado',
        'Empleados_id'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'Empleados_id');
    }

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'responsable_id', 'id');
    }


    public function getEmailAttribute()
    {
        return $this->attributes['correo'];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['correo'] = $value;
    }

    public function getRoleAttribute()
    {
        return $this->attributes['rol'];
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['rol'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['estado'];
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['estado'] = $value;
    }

    public function getEmployeeIdAttribute()
    {
        return $this->attributes['Empleados_id'];
    }

    public function setEmployeeIdAttribute($value)
    {
        $this->attributes['Empleados_id'] = $value;
    }
}
