<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'dni',
        'password',
        'enabled',
        'attention_profile_id',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_attendant_accesses')->withTimestamps();
    }

    public function attentionProfile()
    {
        return $this->belongsTo(AttentionProfile::class);
    }
}
