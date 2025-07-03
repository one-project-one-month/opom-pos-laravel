<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/v1/products", ProductController::class);

Route::post('add-categories',[CategoryController::class,'adding']);
Route::put('update-categories',[CategoryController::class,'update']);
Route::delete('delete-categories',[CategoryController::class,'delete']);
Route::get('getData',[CategoryController::class,'dataGet']);
