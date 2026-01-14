<?php

namespace App\Observers;

use App\Enums\ShiftState;
use App\Models\Qualification;

class QualificationObserver
{
    public function created(Qualification $qualification)
    {
        $shift = $qualification->shift;
        $shift->state = ShiftState::Qualified->value;
        $shift->save();
    }
}
