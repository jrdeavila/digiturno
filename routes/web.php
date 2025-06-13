<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UI\Attention\IndexController as AttentionIndexController;
use App\Http\Controllers\UI\AttentionProfileController;
use App\Http\Controllers\UI\AttentionProfileServiceController;
use App\Http\Controllers\UI\BranchController;
use App\Http\Controllers\UI\ClientController;
use App\Http\Controllers\UI\CustomerReception\CreateShiftController;
use App\Http\Controllers\UI\CustomerReception\IndexController as CustomerReceptionIndexController;
use App\Http\Controllers\UI\DisableModuleController;
use App\Http\Controllers\UI\EnableModuleController;
use App\Http\Controllers\UI\ModuleController;
use App\Http\Controllers\UI\RoomController;
use App\Http\Controllers\UI\ServiceController;
use App\Http\Controllers\UI\ShiftController;
use App\Http\Controllers\UI\ShiftReportController;
use App\Http\Controllers\UI\Waiting\IndexController as WaitingIndexController;
use App\Http\Middleware\CustomerReception\VerifyReceptionModule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('home');
});


Auth::routes();

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::resource('branches', BranchController::class)
            ->names('branches');
        Route::resource('rooms', RoomController::class)
            ->names('rooms');
        Route::resource('branches.rooms', RoomController::class)
            ->names('branches.rooms')
            ->only(['create']);
        Route::resource('clients', ClientController::class)
            ->names('clients');
        Route::resource('modules', ModuleController::class)
            ->names('modules');
        Route::put('/modules/{module}/enable', EnableModuleController::class)->name('modules.enable');
        Route::put('/modules/{module}/disable', DisableModuleController::class)->name('modules.disable');
        Route::resource('attention-profiles', AttentionProfileController::class)
            ->names('attention-profiles');
        Route::get('/shifts/report', ShiftReportController::class)->name('shifts.report');
        Route::resource('shifts', ShiftController::class)
            ->except(['create', 'store', 'edit', 'update'])
            ->names('shifts');
        Route::get('/attention-profiles/{attention_profile}/services', [AttentionProfileServiceController::class, 'edit'])
            ->name('attention-profiles.services.edit');
        Route::put('/attention-profiles/{attention_profile}/services', [AttentionProfileServiceController::class, 'update'])
            ->name('attention-profiles.services.update');
        Route::resource('services', ServiceController::class)->names('services');
    });
    Route::middleware([
        VerifyReceptionModule::class,
    ])->group(function () {
        Route::get('/customer-reception', CustomerReceptionIndexController::class)->name('attention.customer-reception.index');
        Route::post('/customer-reception/create-shift', CreateShiftController::class)->name('attention.customer-reception.create-shift');
    });
    Route::get('/attention', AttentionIndexController::class)->name('attention.attention.index');
    Route::get('/waiting', WaitingIndexController::class)->name('attention.waiting.index');
    Route::get('/home', HomeController::class)->name('home');
});
