<?php

use App\Models\Product;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiscountItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleReportController;
use App\Models\DiscountItem;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/v1/products", ProductController::class);

Route::apiResource('/v1/categories', CategoryController::class);

Route::apiResource('v1/brands', BrandController::class);

// Authentication routes

Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/check-auth', [AuthController::class, 'checkAuth'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});
Route::get("/v1/orders", [SaleReportController::class, 'orders']);
Route::get("/v1/orders_week", [SaleReportController::class, 'orderWeek']);
Route::get("/v1/orders_month", [SaleReportController::class, 'orderMonth']);
Route::get("/v1/total_amount", [SaleReportController::class, 'totalAmount']);
Route::get("/v1/total_week", [SaleReportController::class, 'totalWeek']);
Route::get("/v1/total_month", [SaleReportController::class, 'totalMonth']);
Route::get("/v1/week_gain", [SaleReportController::class, 'weekGain']);
Route::get("/v1/month_gain", [SaleReportController::class, 'monthGain']);

Route::get('/v1/get_weekly_top_sale_items{action?}', [SaleReportController::class, 'getWeeklyTopSaleItems']);
Route::get('/v1/get_weekly_lower_sale_items{action?}', [SaleReportController::class, 'getWeeklyLowerSaleItems']);
Route::get('/v1/get_monthly_top_sale_items{action?}', [SaleReportController::class, 'getMonthlyTopSaleItems']);
Route::get('/v1/get_monthly_lower_sale_items{action?}', [SaleReportController::class, 'getMonthlyLowerSalesItems']);
# download route (sale report)
Route::get('/v1/download/top_lower_sale_reports{time?}{choice?}{action?}', [SaleReportController::class, 'downloadSaleReport']);


Route::apiResource("/v1/discount_items", DiscountItemController::class);
Route::get("/v1/discount_products", [DiscountItemController::class,'discountProducts']);
