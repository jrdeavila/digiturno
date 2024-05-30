<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('attention_profiles', \App\Http\Controllers\AttentionProfileController::class)->names('attention_profiles');
