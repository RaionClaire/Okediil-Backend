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
use App\Http\Controllers\CartController;
use App\Http\Controllers\BiayaController;
use Illuminate\Http\Request;
use App\Http\Controllers\CrmController;

// ============ PUBLIC ROUTES (No Auth Required) ============
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to Okediil API']);
});

Route::get('log-viewers', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);

// Public Routes - Transaction Tracking (for customers only)
Route::post('/transaksi-track', [TransaksiController::class, 'trackByPhone'])
    ->middleware('throttle:10,1');
Route::post('/track-by-phone', [TransaksiController::class, 'trackByPhone'])
    ->middleware('throttle:10,1');

// ============ PROTECTED ROUTES (Auth Required) ============
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth user management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Karyawan routes
    Route::apiResource('/karyawan', KaryawanController::class);
    Route::put('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword']);
    Route::put('/change-password', [KaryawanController::class, 'changePassword']);
    Route::get('/karyawan-filter', [KaryawanController::class, 'filter']);
    Route::get('/karyawan-total', [KaryawanController::class, 'totalKaryawan']);

    // Transaksi routes
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::put('/transaksi/{id}/status', [TransaksiController::class, 'updateStatus']); 
    Route::put('/transaksi/{id}/status-with-notes', [TransaksiController::class, 'updateStatusWithNotes']); 
    Route::get('/transaksi/{id}/status-history', [TransaksiController::class, 'getStatusHistory']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);
    Route::get('/transaksi-filter', [TransaksiController::class, 'filter']);
    Route::get('/transaksi-total', [TransaksiController::class, 'totalTransaksi']);

    // Cart routes
    Route::post('/cart', [CartController::class, 'store']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::get('/cart/{id}', [CartController::class, 'show']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::get('/cart-filter', [CartController::class, 'filter']);

    // Pembelian routes
    Route::post('/pembelian', [PembelianController::class, 'store']);
    Route::get('/pembelian', [PembelianController::class, 'index']);
    Route::get('/pembelian/{id}', [PembelianController::class, 'show']);
    Route::put('/pembelian/{id}', [PembelianController::class, 'update']);
    Route::delete('/pembelian/{id}', [PembelianController::class, 'destroy']);
    Route::get('/pembelian-filter', [PembelianController::class, 'filter']);
    Route::get('/pembelian-total', [PembelianController::class, 'totalPembelian']);
    Route::get('/pembelian-total-ongkir', [PembelianController::class, 'totalOngkir']);
    Route::get('/pembelian-available', [PembelianController::class, 'available']);

    // Customer routes
    Route::post('/customer', [CustomerController::class, 'store']);
    Route::get('/customer', [CustomerController::class, 'index']);
    Route::get('/customer/{id}', [CustomerController::class, 'show']);
    Route::put('/customer/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);
    Route::get('/customer-filter', [CustomerController::class, 'filter']);
    Route::get('/customer-total', [CustomerController::class, 'totalCustomers']);

    // Omal routes
    Route::post('/omal', [OmalController::class, 'store']);
    Route::get('/omal', [OmalController::class, 'index']);
    Route::get('/omal/{id}', [OmalController::class, 'show']);
    Route::put('/omal/{id}', [OmalController::class, 'update']);
    Route::delete('/omal/{id}', [OmalController::class, 'destroy']);
    Route::get('/omal-filter', [OmalController::class, 'filter']);
    Route::get('/omal-total-nominal', [OmalController::class, 'totalNominalOmal']);

    // Aset routes
    Route::post('/aset', [AsetController::class, 'store']);
    Route::get('/aset', [AsetController::class, 'index']);
    Route::get('/aset/{id}', [AsetController::class, 'show']);
    Route::put('/aset/{id}', [AsetController::class, 'update']);
    Route::delete('/aset/{id}', [AsetController::class, 'destroy']);
    Route::get('/aset-filter', [AsetController::class, 'filter']);
    Route::get('/aset-total-nominal', [AsetController::class, 'totalNominalAset']);

    // Pengeluaran routes
    Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show']);
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update']);
    Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy']);
    Route::get('/pengeluaran-filter', [PengeluaranController::class, 'filter']);
    Route::get('/pengeluaran-total', [PengeluaranController::class, 'totalPengeluaran']);

    // Biaya routes
    Route::post('/biaya', [BiayaController::class, 'store']);
    Route::get('/biaya', [BiayaController::class, 'index']);
    Route::get('/biaya/{id}', [BiayaController::class, 'show']);
    Route::put('/biaya/{id}', [BiayaController::class, 'update']);
    Route::delete('/biaya/{id}', [BiayaController::class, 'destroy']);
    Route::get('/biaya-total', [BiayaController::class, 'totalBiaya']);

    // CRM routes
    Route::post('/crm', [CrmController::class, 'store']);
    Route::get('/crm', [CrmController::class, 'index']);
    Route::get('/crm/{id}', [CrmController::class, 'show']);
    Route::put('/crm/{id}', [CrmController::class, 'update']);
    Route::delete('/crm/{id}', [CrmController::class, 'destroy']);
    Route::get('/crm-search', [CrmController::class, 'search']);
    Route::get('/crm-total', [CrmController::class, 'total']);
});