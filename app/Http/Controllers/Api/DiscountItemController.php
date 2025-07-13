<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountItem;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountItemController extends Controller
{
    public function index()
    {
        return DiscountItem::all();
    }

    public function discountProducts()
    {
        return DiscountItem::with('product')->get();
    }

    public function store(Request $request)
    {
        return $request->all();
    }
}
