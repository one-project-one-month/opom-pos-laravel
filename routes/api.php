<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/v1/products", ProductController::class);

Route::apiResource('categories', CategoryController::class);

// Brand Routes
Route::post('add-brands', [BrandController::class, 'adding']);
Route::put('update-brands', [BrandController::class, 'update']);
Route::delete('delete-brands', [BrandController::class, 'delete']);
Route::get('get-brands', [BrandController::class, 'dataGet']);

