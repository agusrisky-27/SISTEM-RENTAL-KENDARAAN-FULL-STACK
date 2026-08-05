<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\PelangganController;
// (Tambahkan controller lain yang diperlukan nanti)

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/kendaraan', [KendaraanController::class, 'store']);
        Route::put('/kendaraan/{id}', [KendaraanController::class, 'update']);
        Route::delete('/kendaraan/{id}', [KendaraanController::class, 'destroy']);
        
        Route::delete('/pelanggan/{id}', [PelangganController::class, 'destroy']);
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);
        
        Route::apiResource('users', \App\Http\Controllers\Api\UserController::class)->except(['show']);
    });

    // Public / Staff Routes (GET, and certain POST/PUT)
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);

    Route::apiResource('pelanggan', PelangganController::class)->except(['destroy']);
    
    Route::apiResource('transaksi', TransaksiController::class)->except(['destroy']);
    
    Route::apiResource('pengembalian', \App\Http\Controllers\Api\PengembalianController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('pembayaran', \App\Http\Controllers\Api\PembayaranController::class)->only(['index', 'store', 'destroy']);
});
