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

            // Calculate total and validate stock
            $total = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check if enough stock is available
                // if ($product->stock < $item['quantity']) {
                //     return response()->json([
                //         'success' => false,
                //         'message' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}"
                //     ], 400);
                // }
                if($product->dis_percent > 0) {
                    // $item['price'] = $item['price'] - $item['price'] * $product->dis_percent/100;
                    $product->price = $product->price - $product->price * $product->dis_percent/100;
                     $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal
                ];
                }
                else {
                     $itemTotal = $product->price * $item['quantity'];
                $total += $itemTotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $itemTotal
                ];
                }
               
            }

            // Calculate change amount
            $changeAmount = $request->paid_amount - $total;

            if ($changeAmount < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient payment amount'
                ], 400);
            }
             $product = Product::find($item['product_id']);
                if($product->stock < $item['quantity']){
                    return response()->json([
                        'status' => false,
                        'message' => $product->name .' is out of stock than your order.',
                        'stock of product' => $product->stock
                    ], 400);
                }
             
            // Generate order number (you can customize this logic)
            // $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
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
                if($product->dis_percent > 0){
                    $item['price'] =  $product->price - $product->price * $product->dis_percent/100;
                    
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total']
                ]);

                // Reduce product stock
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
                    'total_amount' => $order->total,
                    // 'items' => $order->items,
                    'paid_amount' => $order->paid_amount,
                    'change_amount' => $order->change_amount,
                    'payment' => $order->payment->method,
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
    // public function index(Request $request)
    // {
    //     $order = Order::query()->paginate(3);
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'All Order history with paginate.',
    //         'All orders' => $order,
    //     ], 200);

    // }


