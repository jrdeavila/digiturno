<?php


\Illuminate\Support\Facades\Broadcast::channel('rooms.{room}.clients', function (
    \App\Models\User $user,
    \App\Models\Room $room
) {
    return true;
});

\Illuminate\Support\Facades\Broadcast::channel('rooms.{room}.shifts', function (
    \App\Models\User $user,
    \App\Models\Shift $shift
) {
    return true;
});




\Illuminate\Support\Facades\Broadcast::channel('rooms.{room}.attention_profiles.{attentionProfile}.shifts', function (
    \App\Models\User $user,
    \App\Models\Room $room,
    \App\Models\AttentionProfile $attentionProfile
) {
    return true;
});


\Illuminate\Support\Facades\Broadcast::channel('modules.{module}.current-shift', function (
    \App\Models\User $user,
    \App\Models\Module $module
) {
    return true;
});

\Illuminate\Support\Facades\Broadcast::channel('modules.{module}.shifts', function (
    \App\Models\User $user,
    \App\Models\Module $module
) {
    return true;
});

\Illuminate\Support\Facades\Broadcast::channel('modules.{module}', function (
    \App\Models\User $user,
    \App\Models\Module $module
) {
    return true;
});

\Illuminate\Support\Facades\Broadcast::channel('attendants.{attendant}', function (
    \App\Models\User $user,
    \App\Models\Attendant $attendant
) {
    return true;
});
