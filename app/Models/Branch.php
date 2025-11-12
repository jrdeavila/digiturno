<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public $with = ['rooms'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
