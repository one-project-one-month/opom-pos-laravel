<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SaleReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource("/v1/products", ProductController::class);

Route::get("/v1/orders", [SaleReportController::class, 'orders'] );
Route::get("/v1/orders_week", [SaleReportController::class, 'orderWeek'] );
Route::get("/v1/orders_month", [SaleReportController::class, 'orderMonth'] );
Route::get("/v1/totalAmount", [SaleReportController::class, 'totalAmount']);
Route::get("/v1/total_week", [SaleReportController::class, 'totalWeek']);
Route::get("/v1/total_month", [SaleReportController::class, 'totalMonth']);

# top & lower sale report
Route::get('/v1/get_weekly_top_sale_items{action?}',[SaleReportController::class,'getWeeklyTopSaleItems']);
Route::get('/v1/get_weekly_lower_sale_items{action?}',[SaleReportController::class,'getWeeklyLowerSaleItems']);
Route::get('/v1/get_monthly_top_sale_items{action?}',[SaleReportController::class,'getMonthlyTopSaleItems']);
Route::get('/v1/get_monthly_lower_sale_items{action?}',[SaleReportController::class,'getMonthlyLowerSalesItems']);

# download route (sale report)
Route::get('/v1/download/top_lower_sale_reports{time?}{choice?}{action?}',[SaleReportController::class,'downloadSaleReport']);