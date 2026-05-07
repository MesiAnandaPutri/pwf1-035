<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController as ProductApiController;
use App\Http\Controllers\Api\CategoryController as CategoryApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/login', [AuthController::class, 'getToken']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public: lihat produk & kategori tanpa token
Route::get('/product',       [ProductApiController::class,  'index']);
Route::get('/product/{id}',  [ProductApiController::class,  'show']);
Route::get('/category',      [CategoryApiController::class, 'index']);
Route::get('/category/{id}', [CategoryApiController::class, 'show']);

// Protected: butuh token Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Product
    Route::post('/product',          [ProductApiController::class, 'store']);
    Route::put('/product/{id}',      [ProductApiController::class, 'update']);
    Route::delete('/product/{id}',   [ProductApiController::class, 'destroy']);

    // Category
    Route::post('/category',         [CategoryApiController::class, 'store']);
    Route::put('/category/{id}',     [CategoryApiController::class, 'update']);
    Route::delete('/category/{id}',  [CategoryApiController::class, 'destroy']);
});