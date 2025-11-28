<?php

namespace App\Models;

use App\Services\MregEstInscritosQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class MregEstInscrito extends Model
{
  public function newQuery(): Builder
  {
    return MregEstInscritosQuery::unified();
  }

  public function scopeWhereName($query, $name)
  {
    return $query->whereRaw("
    LOWER(name) LIKE LOWER('%$name%') OR 
    LOWER(first_name) LIKE LOWER('%$name%') OR 
    LOWER(last_name) LIKE LOWER('%$name%') OR 
    LOWER(second_name) LIKE LOWER('%$name%') OR 
    LOWER(second_last_name) LIKE LOWER('%$name%')
    ");
  }

  public function scopeWhereIdNumber($query, $idNumber)
  {
    return $query->where('id_number', 'LIKE', "%$idNumber%");
  }
}
