<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('warehouses', WarehouseController::class);

    // Stock Transfer routes
    Route::prefix('stock_transfers')->group(function () {
        Route::get('/', [StockTransferController::class, 'index']);
        Route::get('/statusFilter', [StockTransferController::class, 'statusFilter']);
        Route::post('/store', [StockTransferController::class, 'store']);
        Route::post('/{stockTransfer}/change_status', [StockTransferController::class, 'changeStatus']);
        Route::get('/{stockTransfer}/info_details', [StockTransferController::class, 'infoDetails']);
        Route::post('/{stockTransfer}/cancel_or_return', [StockTransferController::class, 'cancelOrReturn']);
    });
});
