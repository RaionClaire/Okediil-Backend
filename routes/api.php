<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OmalController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TransaksiController;

Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// Protected Routes - Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);
    Route::get('/transaksi-filter', [TransaksiController::class, 'filter']);
    Route::get('/transaksi/test-auth', [TransaksiController::class, 'testAuth']);


// Public Routes - Karyawan
Route::post('/karyawan', [KaryawanController::class, 'store']);
Route::get('/karyawan', [KaryawanController::class, 'index']);
Route::get('/karyawan/{id}', [KaryawanController::class, 'show']);
Route::put('/karyawan/{id}', [KaryawanController::class, 'update']);
Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy']);
Route::put('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword']);
Route::get('/karyawan-filter', [KaryawanController::class, 'filter']);

// Public Routes - Customer
Route::post('/customer', [CustomerController::class, 'store']);
Route::get('/customer', [CustomerController::class, 'index']);
Route::get('/customer/{id}', [CustomerController::class, 'show']);
Route::put('/customer/{id}', [CustomerController::class, 'update']);
Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);
Route::get('/customer-filter', [CustomerController::class, 'filter']);

// Public Routes - Omal
Route::post('/omal', [OmalController::class, 'store']);
Route::get('/omal', [OmalController::class, 'index']);
Route::get('/omal/{id}', [OmalController::class, 'show']);
Route::put('/omal/{id}', [OmalController::class, 'update']);
Route::delete('/omal/{id}', [OmalController::class, 'destroy']);
Route::get('/omal-filter', [OmalController::class, 'filter']);

// Public Routes - Aset
Route::post('/aset', [AsetController::class, 'store']);
Route::get('/aset', [AsetController::class, 'index']);
Route::get('/aset/{id}', [AsetController::class, 'show']);
Route::put('/aset/{id}', [AsetController::class, 'update']);
Route::delete('/aset/{id}', [AsetController::class, 'destroy']);
Route::get('/aset-filter', [AsetController::class, 'filter']);

// Public Routes - Pembelian
Route::post('/pembelian', [PembelianController::class, 'store']);
Route::get('/pembelian', [PembelianController::class, 'index']);
Route::get('/pembelian/{id}', [PembelianController::class, 'show']);
Route::put('/pembelian/{id}', [PembelianController::class, 'update']);
Route::delete('/pembelian/{id}', [PembelianController::class, 'destroy']);
Route::get('/pembelian-filter', [PembelianController::class, 'filter']);

// Public Routes - Pengeluaran
Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);
Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update']);
Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy']);
Route::get('/pengeluaran-filter', [PengeluaranController::class, 'filter']);