<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;


Route::post('/login', [AuthController::class, 'login']);

Route::post('/karyawan', [KaryawanController::class, 'store']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
