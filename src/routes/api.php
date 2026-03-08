<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HotelContractController;
use Illuminate\Support\Facades\Route;

// Health check (public)
Route::get('/health', function () {
    return response()->json([
        'status'  => 'ok',
        'version' => 'v1',
        'time'    => now()->toISOString(),
    ]);
});

Route::prefix('v1')->group(function () {

    // Auth (public)
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // Protected (require token)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });

        // Hotel Contracts
        Route::get('hotel-contracts/stats', [HotelContractController::class, 'stats']);
        Route::get('hotel-contracts', [HotelContractController::class, 'index']);
        Route::post('hotel-contracts', [HotelContractController::class, 'store']);
        Route::get('hotel-contracts/{hotelContract}', [HotelContractController::class, 'show']);
        Route::put('hotel-contracts/{hotelContract}', [HotelContractController::class, 'update']);
        Route::patch('hotel-contracts/{hotelContract}', [HotelContractController::class, 'update']);
        Route::delete('hotel-contracts/{hotelContract}', [HotelContractController::class, 'destroy']);
    });
});