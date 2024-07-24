<?php


\Illuminate\Support\Facades\Broadcast::channel('rooms.{room}.clients', function (
    \App\Models\User $user,
    \App\Models\Room $room
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
