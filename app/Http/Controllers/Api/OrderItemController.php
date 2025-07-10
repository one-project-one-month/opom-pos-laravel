<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderItemController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        //for discount item
        $price = $product->discount_price ?? $product->price;

        $total = $price * $request->quantity;

        $orderItem = OrderItem::create([
            'order_id'   => $request->order_id,
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
            'price'      => $price,
            'total'      => $total,
        ]);

        return response()->json([
            'message' => 'Order item created successfully',
            'data'    => $orderItem,
        ], 201);
    }

    public function index()
    {
        return OrderItem::with('product')->get();
    }

    public function show($id)
    {
        return OrderItem::with('product')->findOrFail($id);
    }
}
