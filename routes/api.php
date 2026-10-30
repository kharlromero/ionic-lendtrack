<?php

use App\Http\Controllers\Api\LoanApiController;
use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

Route::get('/loans', [LoanApiController::class, 'index']);
Route::get('/loans/{loan}', [LoanApiController::class, 'show']);
Route::get('/loans/{loan}/payments', [LoanApiController::class, 'payments']);

Route::get('/reports/collections', [ReportApiController::class, 'collections']);

