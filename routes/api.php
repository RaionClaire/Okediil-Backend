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

    // ========= Superadmin only =========
    Route::middleware('role:superadmin')->group(function () {
        // Karyawan management
        Route::apiResource('/karyawan', KaryawanController::class);
        Route::put('/karyawan/{id}/reset-password', [KaryawanController::class, 'resetPassword']);
        Route::get('/karyawan-filter', [KaryawanController::class, 'filter']);
        Route::get('/karyawan-total', [KaryawanController::class, 'totalKaryawan']);

        // Pembelian
        Route::prefix('/pembelian')->group(function () {
            Route::post('', [PembelianController::class, 'store']);
            Route::get('', [PembelianController::class, 'index']);
            Route::get('{id}', [PembelianController::class, 'show']);
            Route::put('{id}', [PembelianController::class, 'update']);
            Route::delete('{id}', [PembelianController::class, 'destroy']);
            Route::get('filter', [PembelianController::class, 'filter']);
            Route::get('total', [PembelianController::class, 'totalPembelian']);
            Route::get('total-ongkir', [PembelianController::class, 'totalOngkir']);
        });
        // Aliases for totals (karyawan-total style)
        Route::get('/pembelian-available', [PembelianController::class, 'available']);
        Route::get('/pembelian-total', [PembelianController::class, 'totalPembelian']);
        Route::get('/pembelian-total-ongkir', [PembelianController::class, 'totalOngkir']);

        // Omal
        Route::prefix('/omal')->group(function () {
            Route::post('', [OmalController::class, 'store']);
            Route::get('', [OmalController::class, 'index']);
            Route::get('{id}', [OmalController::class, 'show']);
            Route::put('{id}', [OmalController::class, 'update']);
            Route::delete('{id}', [OmalController::class, 'destroy']);
            Route::get('filter', [OmalController::class, 'filter']);
            Route::get('total-nominal', [OmalController::class, 'totalNominalOmal']);
        });
        
        Route::get('/omal-total-nominal', [OmalController::class, 'totalNominalOmal']);

        // Aset
        Route::prefix('/aset')->group(function () {
            Route::post('', [AsetController::class, 'store']);
            Route::get('', [AsetController::class, 'index']);
            Route::get('{id}', [AsetController::class, 'show']);
            Route::put('{id}', [AsetController::class, 'update']);
            Route::delete('{id}', [AsetController::class, 'destroy']);
            Route::get('filter', [AsetController::class, 'filter']);
            Route::get('total-nominal', [AsetController::class, 'totalNominalAset']);
        });
        
        Route::get('/aset-total-nominal', [AsetController::class, 'totalNominalAset']);

        // Pengeluaran
        Route::prefix('/pengeluaran')->group(function () {
            Route::post('', [PengeluaranController::class, 'store']);
            Route::get('', [PengeluaranController::class, 'index']);
            Route::get('{id}', [PengeluaranController::class, 'show']);
            Route::put('{id}', [PengeluaranController::class, 'update']);
            Route::delete('{id}', [PengeluaranController::class, 'destroy']);
            Route::get('filter', [PengeluaranController::class, 'filter']);
            Route::get('total-pengeluaran', [PengeluaranController::class, 'totalPengeluaran']);
        });
        
        Route::get('/pengeluaran-total', [PengeluaranController::class, 'totalPengeluaran']);

        // Biaya
        Route::prefix('/biaya')->group(function () {
            Route::post('', [BiayaController::class, 'store']);
            Route::get('', [BiayaController::class, 'index']);
            Route::get('{id}', [BiayaController::class, 'show']);
            Route::put('{id}', [BiayaController::class, 'update']);
            Route::delete('{id}', [BiayaController::class, 'destroy']);
            Route::get('total-biaya', [BiayaController::class, 'totalBiaya']);
        });
        
        Route::get('/biaya-total', [BiayaController::class, 'totalBiaya']);
    });

    // ========= Shared: superadmin, admin, teknisi =========
    Route::middleware('role:superadmin,admin,teknisi')->group(function () {
        // Change own password
        Route::put('/change-password', [KaryawanController::class, 'changePassword']);

        // Transaksi
        Route::prefix('/transaksi')->group(function () {
            Route::get('', [TransaksiController::class, 'index']);
            Route::post('', [TransaksiController::class, 'store']);
            Route::get('{id}', [TransaksiController::class, 'show']);
            Route::put('{id}', [TransaksiController::class, 'update']);
            Route::put('{id}/status', [TransaksiController::class, 'updateStatus']);
            Route::put('{id}/status-with-notes', [TransaksiController::class, 'updateStatusWithNotes']);
            Route::get('{id}/status-history', [TransaksiController::class, 'getStatusHistory']);
            Route::delete('{id}', [TransaksiController::class, 'destroy']);
            Route::get('filter', [TransaksiController::class, 'filter']);
            Route::get('total-transaksi', [TransaksiController::class, 'totalTransaksi']);
        });
        Route::get('/transaksi-total', [TransaksiController::class, 'totalTransaksi']);

        // Cart
        Route::prefix('/cart')->group(function () {
            Route::post('', [CartController::class, 'store']);
            Route::get('', [CartController::class, 'index']);
            Route::get('{id}', [CartController::class, 'show']);
            Route::put('{id}', [CartController::class, 'update']);
            Route::delete('{id}', [CartController::class, 'destroy']);
            Route::get('filter', [CartController::class, 'filter']);
        });
    });

    // ========= Admin + Superadmin =========
    Route::middleware('role:superadmin,admin')->group(function () {
        // Customer
        Route::prefix('/customer')->group(function () {
            Route::post('', [CustomerController::class, 'store']);
            Route::get('', [CustomerController::class, 'index']);
            Route::get('{id}', [CustomerController::class, 'show']);
            Route::put('{id}', [CustomerController::class, 'update']);
            Route::delete('{id}', [CustomerController::class, 'destroy']);
            Route::get('filter', [CustomerController::class, 'filter']);
        });
        Route::get('/customer-total', [CustomerController::class, 'totalCustomers']);

        // CRM
        Route::prefix('/crm')->group(function () {
            Route::post('', [CrmController::class, 'store']);
            Route::get('', [CrmController::class, 'index']);
            Route::get('{id}', [CrmController::class, 'show']);
            Route::put('{id}', [CrmController::class, 'update']);
            Route::delete('{id}', [CrmController::class, 'destroy']);
            Route::get('search', [CrmController::class, 'search']);
            Route::get('total-crm', [CrmController::class, 'total']);
        });
        
        Route::get('/crm-total', [CrmController::class, 'total']);
    });
});
