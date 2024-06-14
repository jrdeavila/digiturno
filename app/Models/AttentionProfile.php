<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttentionProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'attention_profile_id'
    ];

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function attentionProfile()
    {
        return $this->belongsTo(AttentionProfile::class);
    }

    public function attentionProfiles()
    {
        return $this->hasMany(AttentionProfile::class);
    }
}
