<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'use_qualification_module'
    ];

    public $timestamps = false;

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
