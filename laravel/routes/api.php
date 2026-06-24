<?php

use App\Http\Controllers\AppointmentApiController;
use Illuminate\Support\Facades\Route;

Route::get('/appointments', [AppointmentApiController::class, 'index']);
Route::get('/appointments/{id}', [AppointmentApiController::class, 'show']);
Route::post('/appointments', [AppointmentApiController::class, 'store']);
Route::patch('/appointments/{id}', [AppointmentApiController::class, 'update']);
Route::delete('/appointments/{id}', [AppointmentApiController::class, 'destroy']);
