<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\GalleryController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:api','throttle:6,1')->name('verification.send');
    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed','auth:api')->name('verification.verify');
    Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset');
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token')->middleware('auth:api');
});
//user routes
Route::prefix('user')->middleware(['auth:api'])->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::put('/', [UserController::class, 'update']);
    Route::delete('/', [UserController::class, 'destroy']);
    Route::patch('/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('media')->group(function () {
    Route::post('/', [GalleryController::class, 'store']);
    Route::delete('/{id}', [GalleryController::class, 'destroy']);
    Route::put('/{id}', [GalleryController::class, 'update']);
});
//Route::post('/test', [AuthController::class, 'test']);



