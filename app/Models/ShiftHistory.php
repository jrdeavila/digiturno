<?php

namespace App\Models;

use App\Enums\ShiftState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ShiftUtils;

class ShiftHistory extends Model
{
    use ShiftUtils;

    protected $table = 'shift_histories';

    protected $fillable = [
        'shift_id',
        'state',
        'responsable_id',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function scopeInProgress($query): Builder
    {
        return $query->where('state', ShiftState::InProgress);
    }

    public function scopePending($query): Builder
    {
        return $query->where('state', ShiftState::Pending);
    }

    public function scopeCompleted($query): Builder
    {
        return $query->where('state', ShiftState::Completed);
    }
}
