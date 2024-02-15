<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CategoryController;


Route::prefix('customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::put('/', [CustomerController::class, 'update']);
    Route::delete('/', [CustomerController::class, 'destroy']);
    Route::patch('/change-status', [CustomerController::class, 'changeStatus']);
    Route::get('/search', [CustomerController::class, 'search']);
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/children/{parentId}', [CategoryController::class, 'getChildren']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::delete('/', [CategoryController::class, 'destroy']);
});



