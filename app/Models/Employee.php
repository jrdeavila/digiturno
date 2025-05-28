<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'empleados';
    protected $connection = 'timeit';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public $hidden = [
        'tipodocumento',
        'noDocumento',
        'nombres',
        'apellidos',
        'sexo',
        'fechanacimiento',
        'email',
        'estado',
        'foto',
        'tipofuncionario',
        'Cargos_id',
    ];

    public $appends = [
        'document_type',
        'document_number',
        'full_name',
        'gender',
        'birth_date',
        'email',
        'employee_type',
        'job_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, "Cargos_id");
    }

    public function getDocumentTypeAttribute()
    {
        return $this->attributes['tipodocumento'];
    }

    public function setDocumentTypeAttribute($value)
    {
        $this->attributes['tipodocumento'] = $value;
    }

    public function getDocumentNumberAttribute()
    {
        return $this->attributes['noDocumento'];
    }

    public function setDocumentNumberAttribute($value)
    {
        $this->attributes['noDocumento'] = $value;
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['nombres'] . ' ' . $this->attributes['apellidos'];
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['nombres'] = $value;
    }

    public function getGenderAttribute()
    {
        return $this->attributes['sexo'];
    }

    public function setGenderAttribute($value)
    {
        $this->attributes['sexo'] = $value;
    }

    public function getBirthDateAttribute()
    {
        return $this->attributes['fechanacimiento'];
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['fechanacimiento'] = $value;
    }

    public function getStatusAttribute()
    {
        return $this->attributes['estado'] == 'Activo' ? true : false;
    }

    public function setStatusAttribute(bool $value)
    {
        $this->attributes['estado'] = $value ? 'Activo' : 'Inactivo';
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value;
    }

    public function getJobIdAttribute()
    {
        return $this->attributes['Cargos_id'];
    }

    public function setJobIdAttribute($value)
    {
        $this->attributes['Cargos_id'] = $value;
    }

    public function getEmployeeTypeAttribute()
    {
        return $this->attributes['tipofuncionario'];
    }

    public function setEmployeeTypeAttribute($value)
    {
        $this->attributes['tipofuncionario'] = $value;
    }
}
