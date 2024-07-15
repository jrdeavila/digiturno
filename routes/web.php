<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;

Route::get('/report', \App\Http\Controllers\ReportController::class)->name('report');


Route::get('/', function () {
  return new JsonResponse(['message' => 'Welcome to the API']);
});
