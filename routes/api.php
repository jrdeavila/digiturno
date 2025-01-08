<?php

use App\Models\Room;
use Illuminate\Support\Facades\Route;

Route::middleware([
  \App\Http\Middleware\VerifyModuleIp::class,
])->apiResource('attention_profiles', \App\Http\Controllers\AttentionProfileController::class)->names('attention_profiles');

Route::prefix('admin')->group(function () {
  Route::post('/login', [\App\Http\Controllers\Admin\AuthenticationController::class, 'login'])->name('admin.login');
  Route::middleware([
    \App\Http\Middleware\VerifyAdminToken::class,
  ])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Admin\AuthenticationController::class, 'profile'])->name('admin.profile');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthenticationController::class, 'logout'])->name('admin.logout');
    Route::post('/refresh', [\App\Http\Controllers\Admin\AuthenticationController::class, 'refresh'])->name('admin.refresh');
  });

  Route::get("/attention_profiles", [\App\Http\Controllers\AttentionProfileController::class, 'adminIndex'])->name('attention_profiles.admin');
  Route::post("/attention_profiles", [\App\Http\Controllers\AttentionProfileController::class, 'adminStore'])->name('attention_profiles.store');
  Route::patch("/attention_profiles/{attention_profile}", [\App\Http\Controllers\AttentionProfileController::class, 'adminUpdate'])->name('attention_profiles.update');
  Route::delete("/attention_profiles/{attention_profile}", [\App\Http\Controllers\AttentionProfileController::class, 'adminDestroy'])->name('attention_profiles.destroy');
});

Route::apiResource('services', \App\Http\Controllers\ServiceController::class)->names('services');
Route::apiResource('services.subservices', \App\Http\Controllers\SubserviceController::class)->names('services.subservices')->only(['index']);
Route::apiResource('attention_profiles.services', \App\Http\Controllers\AttentionProfileServiceController::class)->names('attention_profiles.services')->only(['index', 'store', 'destroy']);
Route::apiResource('attention_profiles.rooms', \App\Http\Controllers\RoomAttentionProfileController::class)->names('attention_profiles.rooms')->only(['store', 'update', 'destroy']);
Route::apiResource('client_types', \App\Http\Controllers\ClientTypeController::class)->names('client_types');
Route::apiResource('rooms', \App\Http\Controllers\RoomController::class)->names('rooms');
Route::apiResource('rooms.attention_profiles.shifts', \App\Http\Controllers\RoomShiftController::class)->names('rooms.shifts')->only(['index']);
Route::prefix('rooms')->group(function () {
  Route::get("/{room}/shifts", [\App\Http\Controllers\RoomShiftController::class, 'shiftsByRoom'])->name('rooms.shifts.by_room');
  Route::get('/{room}/shifts/distracted', [\App\Http\Controllers\RoomShiftController::class, 'shiftsDistractedByRoom'])->name('rooms.shifts.distracted_by_room');
  Route::get('/{room}/attention_profiles/{attention_profile}/shifts/distracted', [\App\Http\Controllers\RoomShiftController::class, 'distracted'])->name('rooms.shifts.distracted');
});
Route::middleware([
  \App\Http\Middleware\VerifyModuleIp::class,
])->prefix('shifts')->group(function () {
  Route::post('/with-attention-profile', [\App\Http\Controllers\ShiftController::class, 'createShiftWithAttentionProfile'])->name('shifts.with-attention-profile');
  Route::put('/{shift}/with-attention-profile', [\App\Http\Controllers\ShiftController::class, 'updateShiftWithAttentionProfile'])->name('shifts.update-with-attention-profile');
  Route::get('/distracted', [\App\Http\Controllers\ShiftController::class, 'distracted'])->name('shifts.distracted');
  Route::get('/in-progress', [\App\Http\Controllers\ShiftController::class, 'inProgress'])->name('shifts.in-progress');
  Route::put('/{shift}/completed', [\App\Http\Controllers\ShiftController::class, 'completedShift'])->name('shifts.completed');
  Route::put('/{shift}/qualified', [\App\Http\Controllers\ShiftController::class, 'qualifiedShift'])->name('shifts.qualified');
  Route::put('/{shift}/distracted', [\App\Http\Controllers\ShiftController::class, 'distractedShift'])->name('shifts.distracted');
  Route::put('/{shift}/transfer', [\App\Http\Controllers\ShiftController::class, 'transferShift'])->name('shifts.transfer');
  Route::put('/{shift}/pending', [\App\Http\Controllers\ShiftController::class, 'sendToPending'])->name('shifts.pending');
  Route::put('/{shift}/call', [\App\Http\Controllers\ShiftController::class, 'call'])->name('shifts.call');
  Route::put('/{shift}/in-progress', [\App\Http\Controllers\ShiftController::class, 'sendToInProgress'])->name('shifts.in-progress');
});
Route::apiResource('shifts', \App\Http\Controllers\ShiftController::class)->names('shifts')->only(['index', 'store', 'show', 'destroy']);
Route::apiResource('branches', \App\Http\Controllers\BranchController::class)->names('branches');

Route::middleware([
  \App\Http\Middleware\VerifyModuleIp::class,
])->prefix('modules')->group(function () {
  Route::get('/myself', [\App\Http\Controllers\ModuleController::class, 'mySelf'])->name('modules.myself');
  Route::get('/shifts/current', [\App\Http\Controllers\ModuleShiftController::class, 'currentShift'])->name('modules.current-shift');
  Route::get('/shifts', [\App\Http\Controllers\ModuleShiftController::class, 'myShifts'])->name('modules.my-shifts');
});

Route::get("/modules/shifts/count", [\App\Http\Controllers\ModuleShiftController::class, 'countShiftPerModule'])->name('modules.shifts.count');
Route::apiResource('modules', \App\Http\Controllers\ModuleController::class)->names('modules');

Route::apiResource('module_types', \App\Http\Controllers\ModuleTypeController::class)->only(['index'])->names('module_types');

Route::middleware([
  \App\Http\Middleware\VerifyModuleIp::class,
])->prefix('attendants')->group(function () {
  Route::post('/login', [\App\Http\Controllers\AuthenticationController::class, 'login'])->name('attendants.login');
  Route::middleware([
    \App\Http\Middleware\VerifyAttendantToken::class,
  ])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\AuthenticationController::class, 'profile'])->name('attendants.profile');
    Route::post('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout'])->name('attendants.logout');
    Route::post('/refresh', [\App\Http\Controllers\AuthenticationController::class, 'refresh'])->name('attendants.refresh');
  });
});
Route::apiResource('attendants', \App\Http\Controllers\AttendantController::class)->names('attendants');
Route::apiResource('attendants.absences', \App\Http\Controllers\AttendantAbsenceController::class)->names('attendant.absence')->only(['index', 'store']);
Route::apiResource("attendants.juridical_cases", \App\Http\Controllers\JuridicalCaseController::class)->names('attendants.juridical_cases');
Route::prefix('attendants')->group(function () {
  Route::put('/{attendant}/back-to-work', [\App\Http\Controllers\AttendantAbsenceController::class, 'backToWork'])->name('attendant.back-to-work');
  Route::post("/{attendant}/juridical_cases/{juridical_case}/observations", [\App\Http\Controllers\JuridicalCaseController::class, 'addObservation'])->name('attendants.juridical_cases.observations.store');
  Route::delete("/{attendant}/juridical_cases/observations/{observation}", [\App\Http\Controllers\JuridicalCaseController::class, 'destroyObservation'])->name('attendants.juridical_cases.observations.destroy');
});

Route::apiResource('module-types', \App\Http\Controllers\ModuleTypeController::class)->names('module-types');
Route::apiResource('modules.attendants', \App\Http\Controllers\ModuleAttendantController::class)->names('modules.attendants')->only(['index']);
Route::prefix('clients')->group(function () {
  Route::get('/find', [\App\Http\Controllers\ClientController::class, 'find'])->name('clients.find');
  Route::put('/{client}/restore', [\App\Http\Controllers\ClientController::class, 'restore'])->name('clients.restore');
  Route::delete('/{client}/force-delete', [\App\Http\Controllers\ClientController::class, 'forceDelete'])->name('clients.force-delete');
});
Route::apiResource('clients', \App\Http\Controllers\ClientController::class)->names('clients');

Route::apiResource('absence_reasons', \App\Http\Controllers\AbsenceReasonController::class)->names('absence_reason');
Route::apiResource('attendant_accesses', \App\Http\Controllers\AttendantModuleAccessController::class)->only(['index'])->names('attendant_accesses');

Route::prefix('report')->group(function () {
  Route::get('/', \App\Http\Controllers\ReportController::class)->name('report');
  Route::get('/json', [\App\Http\Controllers\ReportController::class, 'toJson'])->name('report.to-json');
  Route::prefix('cae')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReportController::class, 'toCAE'])->name('report.to-cae');
    Route::get('/json', [\App\Http\Controllers\ReportController::class, 'toCAEJson'])->name('report.to-cae-json');
  });
});

Route::get("/storage/{filepath}", function () {
  return response()->file(storage_path("app/public/" . request()->filepath));
});
