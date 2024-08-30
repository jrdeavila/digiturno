<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attentionProfiles()
    {
        return $this->belongsToMany(AttentionProfile::class, 'room_has_attention_profiles');
    }



    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
}
