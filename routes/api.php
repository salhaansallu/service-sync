<?php

use App\Http\Controllers\PosDataController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ServiceRequestController;
use App\Http\Controllers\Api\WarrantyController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OTPController;
use App\Http\Controllers\Api\AdminBookingController;
use App\Http\Controllers\Api\CustomerOrderController;
use App\Http\Controllers\RepairsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - WeFix.lk
|--------------------------------------------------------------------------
*/

// Public Routes - Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Public Routes - Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{productId}', [ProductController::class, 'show']);
});

Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'create']);
    Route::get('/user', [BookingController::class, 'getUserBookings']);
    Route::get('/{bookingId}', [BookingController::class, 'getBooking']);
    Route::put('/{bookingId}/cancel', [BookingController::class, 'cancelBooking']);
    Route::delete('/{bookingId}', [BookingController::class, 'deleteBooking']);
});

// Public Routes - Warranty
Route::post('/warranty/check', [WarrantyController::class, 'check']);

if (env('N8NAPI', false)) {
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('n8n')->group(function () {
            Route::post('/get-booking', [BookingController::class, 'n8n_get']);
            Route::post('/get-warranty', [WarrantyController::class, 'n8n_get']);
            Route::post('/get-repairs', [RepairsController::class, 'n8n_get']);
        });
    });
}


/*
|--------------------------------------------------------------------------
| Legacy POS Routes
|--------------------------------------------------------------------------
*/
Route::post('website/sync', [ProductsController::class, 'sync']);
Route::get('run-cron', [PosDataController::class, 'runCron']);
