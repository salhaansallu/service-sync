<?php

use App\Http\Controllers\PosDataController;
use App\Http\Controllers\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('website/sync', [ProductsController::class, 'sync']);
Route::get('run-cron', [PosDataController::class, 'runCron']);
// Route::post('website/new-order', [ProductsController::class, 'newOrderSync']);
// Route::post('website/order-sync', [ProductsController::class, 'orderSync']);
