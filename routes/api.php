<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/v1/products", ProductController::class);

Route::apiResource('/v1/categories', CategoryController::class);

Route::apiResource('v1/brands', BrandController::class);

Route::apiResource('v1/payments', PaymentController::class)->only([
    'index', 'show'
]);