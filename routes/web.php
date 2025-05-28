<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchUserToModuleController;
use App\Http\Controllers\UI\AttentionProfileController;
use App\Http\Controllers\UI\BranchController;
use App\Http\Controllers\UI\ClientController;
use App\Http\Controllers\UI\ModuleController;
use App\Http\Controllers\UI\RoomController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('home');
});


Auth::routes();

Route::middleware('auth')->group(function () {
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
    Route::resource('attention-profiles', AttentionProfileController::class)
        ->names('attention-profiles');
    Route::get(trans('home'), HomeController::class)->name('home');
});
