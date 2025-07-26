<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleReportController extends Controller
{
   public function orders(Request $request)
{  

    $orders = Order::query()->when($request->has('order_number'), function($q) use($request) {
        $q->where('order_number', 'like', '%'.$request->order_number.'%');
    })->with(['user', 'customer'])->paginate(5);
    // $orders = Order::with(['user', 'customer'])->paginate(5);

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
       $order = $query->paginate(5);
       return response()->json($order);
    }
     public function orderMonth() {
       $query = Order::query();
       $query->whereMonth('created_at', now()->month());
       $order = $query->paginate(5);
       return response()->json($order);
    }
    public function orderYear() {
    $query = Order::query();
    $now = Carbon::now();
    $startOfYear = $now->copy()->startOfYear();
    $endOfYear = $now->copy()->endOfYear();

    $query->whereBetween('created_at', [$startOfYear, $endOfYear]);
    $order = $query->paginate(5);
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
            
        $OrderItems = $query->paginate(5);
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
        $OrderItems = $query->paginate(5);
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
        $OrderItems = $query->paginate(5);
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
        $OrderItems = $query->paginate(5);
        return response()->json($OrderItems);
    }

    public function downloadSaleReport(Request $request){
        $time = $request->query('time','monthly');
        $choice = $request->query('choice','top');
        $action = $request->query('action','total_quantity');

        $query = OrderItem::query()
                ->select('product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(price) as price'),
                DB::raw('SUM(total) as total')
                )
                ->groupBy('product_id')
                ->with('product')
                ->limit(20);

        if($time == 'weekly'){
            $query->whereBetween('created_at',[now()->startOfWeek(),now()->endOfWeek()]);
        }else{
            $query->whereBetween('created_at',[now()->startOfMonth(),now()->endOfMonth()]);
        }

        if($choice == 'top'){
            $query->orderByDesc($action);
        }else{
            $query->orderBy($action);
        }
        $OrderItems = $query->get();
        // return $OrderItems;
        $pdf = Pdf::loadView('sale_reports',[
            'time' => $time,
            'choice' => $choice,
            'OrderItems' => $OrderItems
        ]);
        return $pdf->download('sale_reports');
    }

}
