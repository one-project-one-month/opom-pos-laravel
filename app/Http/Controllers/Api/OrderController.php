<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function create(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|integer|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'customer_id' => 'nullable|integer|exists:customers,id',
        'payment_id' => 'nullable|integer|exists:payments,id',
        'paid_amount' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Initialize totals
        $total = 0;
        $originalPriceTotal = 0;
        $discountAmountTotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $quantity = $item['quantity'];
            $originalPrice = $product->price;

            // Calculate discount
            if ($product->dis_percent > 0) {
                $discountedPrice = $originalPrice - ($originalPrice * $product->dis_percent / 100);
            } else {
                $discountedPrice = $originalPrice;
            }

            // Totals
            $originalItemTotal = $originalPrice * $quantity;
            $discountedItemTotal = $discountedPrice * $quantity;

            $originalPriceTotal += $originalItemTotal;
            $total += $discountedItemTotal;
            $discountAmountTotal += $originalItemTotal - $discountedItemTotal;

            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'price' => $discountedPrice,
                'total' => $discountedItemTotal
            ];
        }

        // Check payment sufficiency
        $changeAmount = $request->paid_amount - $total;

        if ($changeAmount < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient payment amount'
            ], 400);
        }

        // Check stock again just before saving (safety)
        foreach ($orderItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'status' => false,
                    'message' => $product->name . ' is out of stock than your order.',
                    'stock of product' => $product->stock
                ], 400);
            }
        }

        // Generate order number
        $orderNumber = OrderService::generateOrderNumber();

        // Create the order
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(),
            'customer_id' => $request->customer_id,
            'total' => $total,
            'payment_id' => $request->payment_id,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $changeAmount,
        ]);

        // Create order items and reduce stock
        foreach ($orderItems as $item) {
            $product = Product::find($item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total']
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        // Load relationships for response
        $order->load(['items.product', 'customer', 'user']);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => $order,
                'staffRole' => $order->user->getRoleNames(),
                'receipt_number' => $order->order_number,
                'original_price_total' => $originalPriceTotal,
                'discount_amount_total' => $discountAmountTotal,
                'total_amount' => $order->total,
                'paid_amount' => $order->paid_amount,
                'change_amount' => $order->change_amount,
                'payment' => $order->payment->method ?? null,
                'items_count' => $order->items->count()
            ]
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to create order',
            'error' => $e->getMessage()
        ], 500);
    }
}

}


