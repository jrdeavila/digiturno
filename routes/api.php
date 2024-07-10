<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('attention_profiles', \App\Http\Controllers\AttentionProfileController::class)->names('attention_profiles');
Route::apiResource('services', \App\Http\Controllers\ServiceController::class)->names('services');
Route::apiResource('services.subservices', \App\Http\Controllers\SubserviceController::class)->names('services.subservices')->only(['index']);
Route::apiResource('attention_profiles.services', \App\Http\Controllers\AttentionProfileServiceController::class)->names('attention_profiles.services')->only(['index', 'store', 'destroy']);
Route::apiResource('client_types', \App\Http\Controllers\ClientTypeController::class)->names('client_types');
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class)->names('rooms');
Route::apiResource('shifts', \App\Http\Controllers\ShiftController::class)->names('shifts');
Route::apiResource('branches', \App\Http\Controllers\BranchController::class)->names('branches');
Route::apiResource('modules', \App\Http\Controllers\ModuleController::class)->names('modules');
Route::apiResource('attendants', \App\Http\Controllers\AttendantController::class)->names('attendants');
Route::apiResource('modules.attendants', \App\Http\Controllers\ModuleAttendantController::class)->names('modules.attendants')->only(['index']);
Route::apiResource('clients', \App\Http\Controllers\ClientController::class)->names('clients');
Route::prefix('clients')->group(function () {
  Route::put('/{client}/restore', [\App\Http\Controllers\ClientController::class, 'restore'])->name('clients.restore');
  Route::delete('/{client}/force-delete', [\App\Http\Controllers\ClientController::class, 'forceDelete'])->name('clients.force-delete');
});
