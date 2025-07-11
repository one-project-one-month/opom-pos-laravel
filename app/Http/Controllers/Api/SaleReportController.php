<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;


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
       $week = OrderItem::with('product')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        $cost = $week->map(function ($item) {
        return optional($item->product)->const_price;
        })->filter()->sum();
        
        $price = $week->map(function ($item) {

        return optional($item->product)->price;
        })->filter()->sum();
       
        $gain = $price - $cost;

    return response()->json([
        'total_cost' => $cost,
        'total_price' => $price,
        'gain' => $gain
    ]);
}
 public function monthGain() {
       $month = OrderItem::with('product')->whereMonth('created_at', now()->month)->get();
        $cost = $month->map(function ($item) {
        return optional($item->product)->const_price;
        })->filter()->sum();
        
        $price = $month->map(function ($item) {

        return optional($item->product)->price;
        })->filter()->sum();
       
        $gain = $price - $cost;

    return response()->json([
        'total_cost' => $cost,
        'total_price' => $price,
        'gain' => $gain
    ], 200);
}
    
    public function getWeeklyTopSaleItems(Request $request){
        $action = $request->query('action','quantity');
        
        if($action == 'quantity'){
            $query = OrderItem::query()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->with('product')
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(price) as price'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->orderByDesc('total_quantity');
        }else{
            $query = OrderItem::query()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->with('product')
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(price) as price'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->orderByDesc('total');
        }
            
        $OrderItems = $query->get();
        return response()->json($OrderItems);
        
    }

    public function getWeeklyLowerSaleItems(Request $request){
        $action = $request->query('action','quantity');
        if($action == 'quantity'){
            $query = OrderItem::query()
                ->whereBetween('created_at', [now()->startOfWeek(),now()->endOfWeek()])
                ->with('product')
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(price) as price'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->orderBy('total_quantity')
                ->limit(10);
        }else{
            $query = OrderItem::query()
                ->whereBetween('created_at', [now()->startOfWeek(),now()->endOfWeek()])
                ->with('product')
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(price) as price'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->orderBy('total')
                ->limit(10);
        }
        $OrderItems = $query->get();
        return response()->json($OrderItems);

    }

    public function getMonthlyTopSaleItems(Request $request){
        $action = $request->query('action','quantity');
        if($action == 'quantity'){
            $query = OrderItem::query()
            ->whereBetween('created_at',[now()->startOfMonth(),now()->endOfMonth()])
            ->with('product')
            ->select('product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(price) as price'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_quantity');
        }else{
            $query = OrderItem::query()
                ->whereBetween('created_at',[now()->startOfMonth(),now()->endOfMonth()])
                ->with('product')
                ->select('product_id',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(price) as price'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->orderByDesc('total');   
        }
        $OrderItems = $query->get();
        return response()->json($OrderItems);
    }

    public function getMonthlyLowerSalesItems(Request $request){
        $action = $request->query('action',"quantity");
        if($action == "quantity"){
            $query = OrderItem::query()
                    ->whereBetween('created_at',[now()->startOfMonth(),now()->endOfMonth()])
                    ->with('product')
                    ->select('product_id',
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('MAX(price) as price'),
                        DB::raw('SUM(total) as total')
                    )
                    ->groupBy('product_id')
                    ->orderBy('total_quantity')
                    ->limit('10');
        }else{
            $query = OrderItem::query()
            ->whereBetween('created_at',[now()->startOfMonth(),now()->endOfMonth()])
            ->with('product')
            ->select('product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(price) as price'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('product_id')
            ->orderBy('total')
            ->limit('10');
        }
        $OrderItems = $query->get();
        return response()->json($OrderItems);
    }

}
