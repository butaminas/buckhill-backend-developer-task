<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

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

Route::prefix('v1')->group(function () {

    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware(['isAdmin'])->group(function () {
            Route::get('/logout', [AuthController::class, 'logout']);
            Route::post('/create', [AdminController::class, 'store']);
            Route::get('/user-listing', [AdminController::class, 'index']);
            Route::put('/user-edit/{uuid}', [AdminController::class, 'update']);
            Route::delete('/user-delete/{uuid}', [AdminController::class, 'destroy']);
        });
    });

    Route::prefix('user')->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::get('/', [UsersController::class, 'myDetails']);
            Route::delete('/', [UsersController::class, 'destroy']);
            Route::put('/edit', [UsersController::class, 'update']);
        });

        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('/reset-password-token', [ForgotPasswordController::class, 'reset']);

        Route::post('/create', [UsersController::class, 'store']);
    });
});
