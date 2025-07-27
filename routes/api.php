 <?php

    use App\Models\Product;
    use App\Http\Controllers\Api\SaleReportController;
    use App\Http\Controllers\Api\BrandController;
    use App\Http\Controllers\Api\AuthController;
    use App\Http\Controllers\Api\DiscountItemController;
    use App\Http\Controllers\Api\OrderController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\ProductController;
    use App\Http\Controllers\Api\CategoryController;
    use App\Http\Controllers\Api\OrderItemController;
    use App\Http\Controllers\Api\UserController;
    use App\Http\Controllers\Api\CustomerController;
    use App\Http\Controllers\Api\PaymentController;
use App\Http\Middleware\PreventRefreshTokenAccess;

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

        Route::get('/v1/auth/refresh', [AuthController::class, 'refreshToken']);
        // Route::middleware(['auth:sanctum', 'abilities:refresh'])->post('/v1/auth/refresh', [AuthController::class, 'refreshToken']);


        Route::prefix('v1/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/check-auth', [AuthController::class, 'checkAuth'])->middleware('auth:sanctum');
        Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum', PreventRefreshTokenAccess::class])->group(function () {
   
        Route::apiResource("/v1/products", ProductController::class);
    Route::get('/v1/manager_products', [ProductController::class, 'managerOfProduct']);
    Route::get('v1/all_staff', [UserController::class, 'index'])->middleware('auth:sanctum');
    Route::post('v1/suspended/{id}', [UserController::class, 'suspended'])->middleware('auth:sanctum');
    Route::post('v1/unsuspended/{id}', [UserController::class, 'unsuspended'])->middleware('auth:sanctum');
    Route::get('/v1/dis_products', [ProductController::class, 'discount']);
    Route::post('/v1/products/{id}', [ProductController::class, 'update']);

    Route::apiResource('/v1/categories', CategoryController::class);
    Route::post('/v1/categories/{id}', [CategoryController::class, 'update']);

    Route::apiResource('v1/brands', BrandController::class);

    // Authentication routes

    

    //orderitem
    Route::apiResource('/v1/order-items', OrderItemController::class)->only(['index', 'store', 'show']);
    Route::get("/v1/orders", [SaleReportController::class, 'orders']);
    Route::get("/v1/orders_day", [SaleReportController::class, 'orderDay']);
    Route::get("/v1/orders_week", [SaleReportController::class, 'orderWeek']);
    Route::get("/v1/orders_month", [SaleReportController::class, 'orderMonth']);
    Route::get("/v1/total_amount", [SaleReportController::class, 'totalAmount']);
    Route::get("v1/orders_year", [SaleReportController::class, 'orderYear']);
    Route::get("/v1/total_day", [SaleReportController::class, 'totalDay']);
    Route::get("/v1/total_week", [SaleReportController::class, 'totalWeek']);
    Route::get("/v1/total_month", [SaleReportController::class, 'totalMonth']);
    Route::get("/v1/day_gain", [SaleReportController::class, 'dayGain']);
    Route::get("/v1/week_gain", [SaleReportController::class, 'weekGain']);
    Route::get("/v1/month_gain", [SaleReportController::class, 'monthGain']);
    Route::get("/v1/total_gain", [SaleReportController::class, 'gain']);

    Route::get('/v1/get_weekly_top_sale_items{action?}', [SaleReportController::class, 'getWeeklyTopSaleItems']);
    Route::get('/v1/get_weekly_lower_sale_items{action?}', [SaleReportController::class, 'getWeeklyLowerSaleItems']);
    Route::get('/v1/get_monthly_top_sale_items{action?}', [SaleReportController::class, 'getMonthlyTopSaleItems']);
    Route::get('/v1/get_monthly_lower_sale_items{action?}', [SaleReportController::class, 'getMonthlyLowerSalesItems']);
    # download route (sale report)
    Route::get('/v1/download/top_lower_sale_reports{time?}{choice?}{action?}', [SaleReportController::class, 'downloadSaleReport']);

    // Route::apiResource('v1/brands', BrandController::class);
    Route::apiResource('v1/payments', PaymentController::class);
    Route::post('/v1/payments/{id}', [PaymentController::class, 'update']);

    // Route::get('/v1/orders', [OrderController::class, 'index']);


    // Route::apiResource("/v1/discount_items", DiscountItemController::class);
    //
    // Route::get("/v1/discount_products", [DiscountItemController::class, 'discountProducts']);

    // Order routes
    Route::prefix('v1/orders')->group(function () {
        Route::post('/checkout', [OrderController::class, 'create'])->middleware('auth:sanctum');
    });
    // Route::post('/v1/discount_items/add_products', [DiscountItemController::class, 'productAddToDiscount']);
    // Route::put('/v1/discount_items/product_update/{discountId}', [DiscountItemController::class, 'discountedProductUpdate']);

    Route::apiResource('v1/customers', CustomerController::class);

    });

    // Route::middleware(['auth:sanctum', 'ability:*'])->group(function () {
    

    // });

    
