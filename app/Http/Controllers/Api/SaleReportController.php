<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Order_item;
use Barryvdh\DomPDF\Facade\Pdf;

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
    
    public function getWeeklyTopSaleItems(Request $request){
        $action = $request->query('action','quantity');
        
        if($action == 'quantity'){
            $query = Order_item::query()
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
            $query = Order_item::query()
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
            
        $order_items = $query->get();
        return response()->json($order_items);
        
    }

    public function getWeeklyLowerSaleItems(Request $request){
        $action = $request->query('action','quantity');
        if($action == 'quantity'){
            $query = Order_item::query()
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
            $query = Order_item::query()
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
        $order_items = $query->get();
        return response()->json($order_items);

    }

    public function getMonthlyTopSaleItems(Request $request){
        $action = $request->query('action','quantity');
        if($action == 'quantity'){
            $query = Order_item::query()
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
            $query = Order_item::query()
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
        $order_items = $query->get();
        return response()->json($order_items);
    }

    public function getMonthlyLowerSalesItems(Request $request){
        $action = $request->query('action',"quantity");
        if($action == "quantity"){
            $query = Order_item::query()
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
            $query = Order_item::query()
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
        $order_items = $query->get();
        return response()->json($order_items);
    }

    public function downloadSaleReport(Request $request){
        $time = $request->query('time','monthly');
        $choice = $request->query('choice','top');
        $action = $request->query('action','total_quantity');

        $query = Order_item::query()
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
        $order_items = $query->get();

        $pdf = Pdf::loadView('sale_reports',[
            'time' => $time,
            'choice' => $choice,
            'order_items' => $order_items
        ]);
        return $pdf->download('sale_reports');
    }
}
