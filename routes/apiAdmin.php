<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;

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

Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}/detail', [ProductController::class, 'show']);
    Route::put('/', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::patch('/change-status/{id}', [ProductController::class, 'changeStatus']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::prefix('category')->group(function () {
        Route::post('/', [ProductCategoryController::class, 'store']);
        Route::put('/', [ProductCategoryController::class, 'update']);
    });


    Route::post('/image', [ProductCategoryController::class, 'storeImage']);
});


