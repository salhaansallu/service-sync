<?php

use App\Http\Controllers\DeploymentController;
use Illuminate\Support\Facades\Route;

Route::get('/deploy', [DeploymentController::class, 'index'])->name('deployment.index');
Route::post('/deploy', [DeploymentController::class, 'start'])->middleware('throttle:5,1')->name('deployment.start');
Route::get('/deploy/status/{id}', [DeploymentController::class, 'status'])->whereUuid('id')->name('deployment.status');
