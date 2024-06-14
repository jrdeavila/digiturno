<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('attention_profiles', \App\Http\Controllers\AttentionProfileController::class)->names('attention_profiles');
Route::apiResource('client_types', \App\Http\Controllers\ClientTypeController::class)->names('client_types');
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class)->names('rooms');
Route::apiResource('shifts', \App\Http\Controllers\ShiftController::class)->names('shifts');
