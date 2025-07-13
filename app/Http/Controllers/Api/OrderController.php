<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;


class OrderController extends Controller
{   
    // public function index(Request $request)
    // {
    //     $order = Order::query()->paginate(3);
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'All Order history with paginate.',
    //         'All orders' => $order,
    //     ], 200);

    // }
}
