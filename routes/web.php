<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

Route::get('/report', \App\Http\Controllers\ReportController::class)->name('report');
