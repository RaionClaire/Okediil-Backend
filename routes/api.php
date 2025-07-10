<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;


Route::post('/login', [AuthController::class, 'login']);


// Karyawan
Route::post('/karyawan', [KaryawanController::class, 'store']);
Route::get('/karyawan', [KaryawanController::class, 'index']);
Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
Route::put('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword']);
Route::get('/karyawan-filter', [KaryawanController::class, 'filter']);


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
