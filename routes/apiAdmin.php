<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BrandController;

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
    Route::get('/all', [CategoryController::class, 'getAll']);
    Route::get('/children/{parentId}', [CategoryController::class, 'getChildren']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
    Route::patch('/change-status/{id}', [CategoryController::class, 'changeStatus']);
});

Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}/detail', [ProductController::class, 'show']);
    Route::put('/', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::patch('/change-status/{id}', [ProductController::class, 'changeStatus']);
    Route::patch('/change-featured/{id}', [ProductController::class, 'changeFeatured']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::delete('/gallery/{id}', [\App\Http\Controllers\Admin\GalleryController::class, 'destroy']);
});

Route::prefix('brand')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/all', [BrandController::class, 'getAll']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::post('/', [BrandController::class, 'store']);
    Route::put('/{id}', [BrandController::class, 'update']);
    Route::delete('/{id}', [BrandController::class, 'delete']);
    Route::get('/search', [BrandController::class, 'search']);
    Route::patch('/change-status/{id}', [BrandController::class, 'changeStatus']);
    Route::patch('/change-featured/{id}', [BrandController::class, 'changeFeatured']);
});

