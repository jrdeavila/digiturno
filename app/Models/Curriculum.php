<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
  protected $table = 'hojasdevidas';
  protected $connection = 'timeit';

  protected $hidden = [
    'empleados_id',
    'nivelacademico',
    'correo',
    'telefono',
    'ext',
    'foto',
    'municipio_id',
    'valorcontrato',
    'objeto',
    'fondo_pensiones',
  ];

  protected $appends = [
    'academic_level',
    'email',
    'phone',
    'extension',
    'photo',
    'municipality_id',
    'employee_id',
    'contract_value',
    'object',
    'pension_fund',
  ];


  public function getAcademicLevelAttribute()
  {
    return $this->nivelacademico;
  }
  public function setAcademicLevelAttribute($value)
  {
    $this->nivelacademico = $value;
  }

  public function getEmailAttribute()
  {
    return $this->correo;
  }
  public function setEmailAttribute($value)
  {
    $this->correo = $value;
  }

  public function getPhoneAttribute()
  {
    return $this->telefono;
  }
  public function setPhoneAttribute($value)
  {
    $this->telefono = $value;
  }

  public function getExtensionAttribute()
  {
    return $this->ext;
  }
  public function setExtensionAttribute($value)
  {
    $this->ext = $value;
  }

  public function getPhotoAttribute()
  {
    return env("TIMEIT_PHOTO_URL") . $this->foto;
  }
  public function setPhotoAttribute($value)
  {
    $this->foto = $value;
  }

  public function getMunicipalityIdAttribute()
  {
    return $this->municipio_id;
  }
  public function setMunicipalityIdAttribute($value)
  {
    $this->municipio_id = $value;
  }

  public function getEmployeeIdAttribute()
  {
    return $this->empleados_id;
  }
  public function setEmployeeIdAttribute($value)
  {
    $this->empleados_id = $value;
  }

  public function getContractValueAttribute()
  {
    return $this->valorcontrato;
  }
  public function setContractValueAttribute($value)
  {
    $this->valorcontrato = $value;
  }

  public function getObjectAttribute()
  {
    return $this->objeto;
  }
  public function setObjectAttribute($value)
  {
    $this->objeto = $value;
  }

  public function getPensionFundAttribute()
  {
    return $this->fondo_pensiones;
  }
  public function setPensionFundAttribute($value)
  {
    $this->fondo_pensiones = $value;
  }
}
