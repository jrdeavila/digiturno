<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class MregEstInscritosQuery
{
  public static function unified(): Builder
  {
    $columns = [
      'matricula' => [
        'alias' => 'id',
        'cast' => 'integer',
      ],
      'organizacion' => [
        'alias' => 'organization',
        'cast' => 'string',
      ],
      'razonsocial' => [
        'alias' => 'name',
        'cast' => 'string',
      ],
      'nombre1' => [
        'alias' => 'first_name',
        'cast' => 'string',
      ],
      'nombre2' => [
        'alias' => 'second_name',
        'cast' => 'string',
      ],
      'apellido1' => [
        'alias' => 'last_name',
        'cast' => 'string',
      ],
      'apellido2' => [
        'alias' => 'second_last_name',
        'cast' => 'string',
      ],
      "numid" => [
        'alias' => 'id_number',
        'cast' => 'integer',
      ],
      "ctrafiliacion" => [
        'alias' => 'is_member',
        'cast' => 'boolean',
      ],
      "anorenaflia" => [
        'alias' => 'is_member_since',
        'cast' => 'integer',
      ]
    ];

    $select = function () use ($columns) {
      $selects = [];
      foreach ($columns as $col => $cfg) {
        $alias = $cfg['alias'];
        if (isset($cfg['cast'])) {
          if ($cfg['cast'] == 'boolean') {
            $selects[] = DB::raw("CASE WHEN $col = '1' THEN 1 ELSE 0 END as $alias");
            continue;
          }
          if ($cfg['cast'] == 'integer') {
            $selects[] = DB::raw("CAST($col as SIGNED) as $alias");
            continue;
          }
          $selects[] = DB::raw("$col as $alias");
        }
      }
      return DB::connection('sii')->table("mreg_est_inscritos")
        ->select(array_merge($selects));
    };

    $base = $select();

    return DB::connection('sii')->query()->fromSub($base, 'v_mreg_est_inscritos');
  }
}
