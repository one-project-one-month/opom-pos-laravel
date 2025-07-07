<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Order_item;


class SaleReportController extends Controller
{
   public function orders()
{
    $orders = Order::with(['user', 'customer'])->get();

    $userNames = $orders->pluck('user.name')->unique()->values();
    $customerNames = $orders->pluck('customer.name')->unique()->values();

    return response()->json([
        'orders' => $orders,
        'user_names' => $userNames,
        'customer_names' => $customerNames
    ]);
}

    public function orderWeek() {
       $query = Order::query();
       $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
       $order = $query->get();
       return response()->json($order);
    }
     public function orderMonth() {
       $query = Order::query();
       $query->whereMonth('created_at', now()->month);
       $order = $query->get();
       return response()->json($order);
    }
    public function totalAmount() {
        $order = Order::all();
        $totalAmount = $order->sum('total');

        return response()->json($totalAmount);

    }
     public function totalWeek() {
        $query = Order::query();
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        $total = $query->get();
        $totalAmount = $total->sum('total');

        return response()->json($totalAmount);

    }
    public function totalMonth() {
        $query = Order::query();
        $query->whereMonth('created_at', now()->month);
        $total = $query->get();
        $totalAmount = $total->sum('total');
        return response()->json($totalAmount);
    }

    public function weekGain() {
       $week = Order_item::with('product')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $cost = $week->map(function ($item) {
        return optional($item->product)->const_price;
        })->filter()->sum();
        
        $price = $week->map(function ($item) {
        
        return optional($item->product)->price;
        })->filter()->sum();
       
        $gain = $price - $cost;

    return response()->json([
        'gain' => $gain,
        'total_cost' => $cost,
        'total_price' => $price
    ]);
            
        
    }

}
