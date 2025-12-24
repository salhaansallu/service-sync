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

// Protected Routes - Require Authentication
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::prefix('users')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::put('/change-password', [UserController::class, 'changePassword']);
    });

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{itemId}', [CartController::class, 'updateItem']);
        Route::delete('/items/{itemId}', [CartController::class, 'removeItem']);
        Route::delete('/', [CartController::class, 'clearCart']);
    });

    // Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::put('/{addressId}', [AddressController::class, 'update']);
        Route::delete('/{addressId}', [AddressController::class, 'destroy']);
        Route::put('/{addressId}/default', [AddressController::class, 'setDefault']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{notificationId}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // OTP Verification
    Route::prefix('otp')->group(function () {
        Route::post('/send', [OTPController::class, 'send']);
        Route::post('/verify', [OTPController::class, 'verify']);
    });

    // Customer Orders & Repairs
    Route::prefix('customer')->group(function () {
        Route::get('/orders', [CustomerOrderController::class, 'getOrders']);
        Route::get('/orders/{orderId}', [CustomerOrderController::class, 'getOrder']);
        Route::get('/repairs/{repairId}', [CustomerOrderController::class, 'getRepair']);
        Route::get('/stats', [CustomerOrderController::class, 'getStats']);
    });

    // Admin Routes - Require Admin Role
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Bookings Management
        Route::prefix('bookings')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index']);
            Route::put('/{bookingId}/status', [AdminBookingController::class, 'updateStatus']);
        });

        // Service Requests Management
        Route::prefix('service-requests')->group(function () {
            Route::get('/', [ServiceRequestController::class, 'index']);
            Route::put('/{requestId}', [ServiceRequestController::class, 'update']);
        });

        // Notifications Management
        Route::post('/notifications', [NotificationController::class, 'send']);
    });
});

/*
|--------------------------------------------------------------------------
| Legacy POS Routes
|--------------------------------------------------------------------------
*/
Route::post('website/sync', [ProductsController::class, 'sync']);
Route::get('run-cron', [PosDataController::class, 'runCron']);
