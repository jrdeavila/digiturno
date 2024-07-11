<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('attention_profiles', \App\Http\Controllers\AttentionProfileController::class)->names('attention_profiles');
Route::apiResource('services', \App\Http\Controllers\ServiceController::class)->names('services');
Route::apiResource('services.subservices', \App\Http\Controllers\SubserviceController::class)->names('services.subservices')->only(['index']);
Route::apiResource('attention_profiles.services', \App\Http\Controllers\AttentionProfileServiceController::class)->names('attention_profiles.services')->only(['index', 'store', 'destroy']);
Route::apiResource('client_types', \App\Http\Controllers\ClientTypeController::class)->names('client_types');
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class)->names('rooms');
Route::prefix('shifts')->group(function () {
  Route::get('/distracted', [\App\Http\Controllers\ShiftController::class, 'distracted'])->name('shifts.distracted');
  Route::get('/in-progress', [\App\Http\Controllers\ShiftController::class, 'inProgress'])->name('shifts.in-progress');
  Route::put('/{shift}/completed', [\App\Http\Controllers\ShiftController::class, 'completedShift'])->name('shifts.completed');
  Route::put('/{shift}/qualified', [\App\Http\Controllers\ShiftController::class, 'qualifiedShift'])->name('shifts.qualified');
  Route::put('/{shift}/distracted', [\App\Http\Controllers\ShiftController::class, 'distractedShift'])->name('shifts.distracted');
});
Route::apiResource('shifts', \App\Http\Controllers\ShiftController::class)->names('shifts')->only(['index', 'store', 'show']);
Route::apiResource('branches', \App\Http\Controllers\BranchController::class)->names('branches');

Route::prefix('modules')->group(function () {
  Route::get('/ip-address', [\App\Http\Controllers\ModuleController::class, 'getByIpAddress'])->name('modules.ip-address');
});
Route::apiResource('modules', \App\Http\Controllers\ModuleController::class)->names('modules');


Route::apiResource('attendants', \App\Http\Controllers\AttendantController::class)->names('attendants');
Route::apiResource('modules.attendants', \App\Http\Controllers\ModuleAttendantController::class)->names('modules.attendants')->only(['index']);
Route::apiResource('clients', \App\Http\Controllers\ClientController::class)->names('clients');
Route::prefix('clients')->group(function () {
  Route::put('/{client}/restore', [\App\Http\Controllers\ClientController::class, 'restore'])->name('clients.restore');
  Route::delete('/{client}/force-delete', [\App\Http\Controllers\ClientController::class, 'forceDelete'])->name('clients.force-delete');
});
