<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function discount(Request $request)
    {
         $query = Product::query();
        $discountCount = $query->where('dis_percent','>' ,0)->get()->count();
         $discountCountOfCategory = $query->where('dis_percent','>' ,0)->when($request->has('category_name'), function($q) use($request){
            $q->whereHas('category', function($name) use($request){
                $name->where('name', 'like', '%'.$request->category_name.'%');
            });
        })->count();
        $discount = $query->where('dis_percent','>' ,0)->when($request->has('category_name'), function($q) use($request){
            $q->whereHas('category', function($name) use($request){
                $name->where('name', 'like', '%'.$request->category_name.'%');
            });
        })->paginate(5);
        return response()->json([
        'status' => true,
        'message' => 'discount items',
        'discount_products_count' => $discountCount,
        'discount_products_count of category' => $discountCountOfCategory,
        'discount_products' => $discount,
        
        ],200);
       
    }

    public function index(Request $request)
    {  
        $products = Product::query()->when($request->has('name'), function($q) use($request){
        $q->where('name','like', '%'.$request->name.'%');
        })->when($request->has('id'), function($p) use($request){
        $p->where('id', 'like', '%'.$request->id.'%');
        })->when($request->has('category_name'), function($r) use($request){
        $r->whereHas('category', function($name) use ($request){
        $name->where('name', 'like', '%'.$request->category_name.'%');
        });
        })->get();
        
        return response()->json([
        'status' =>  true,
        'message' => 'success filter',
        'products' => $products
    ],200);
       
    }
    
    public function managerOfProduct(Request $request)
{   
        $query = Product::query(); 
    
    if ($request->has('status')) {
        $status = $request->status;

        // Custom logic based on status
       if ($status === 'out_of_stock') {
    $query->where('stock', '=', 0);
} elseif ($status === 'low_stock') {
    $query->whereBetween('stock', [1, 49]);
} elseif ($status === 'full_stock') {
    $query->where('stock', '>=', 50);
}

    }
    // $products = $query->paginate(5);

        $query->when($request->has('name'), function($q) use($request){
        $q->where('name','like', '%'.$request->name.'%');
        })->when($request->has('id'), function($p) use($request){
        $p->where('id', 'like', '%'.$request->id.'%');
        })->when($request->has('category_name'), function($r) use($request){
        $r->whereHas('category', function($name) use ($request){
        $name->where('name', 'like', '%'.$request->category_name.'%');
        });
        })
        ->get();
        $product = Product::all();
        $outOfStock = Product::where('stock', '=', 0)->get();
        $lowOfStock = Product::where('stock', '>', 0)->where('stock', '<', 50)->get();
        $fullOfStock = Product::where('stock', '>=', 50)->get();
        $countOfOutOfStock = count($outOfStock);
        $countLowStock = count($lowOfStock);
        $countFullStock = count($fullOfStock);
        $totalProduct = count($product);
        $pageSize = $request->get('pageSize'); // default 10
        $products = $query->paginate($pageSize);
    // $products = $query->get();
        return response()->json([
        "status" => true,
        "message" => "Products status and filter",
        "products" => $products,
        'count of total products' => $totalProduct,
        'count of out of stock' => $countOfOutOfStock,
        'count of low of stock' => $countLowStock,
        'count of full of stock' => $countFullStock,
        'Out of stock' => $outOfStock,
        'Low stock' => $lowOfStock,
        'Full stock' => $fullOfStock,
        ], 200);
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Product::validationRules());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
        }

        try {
            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'const_price' => $request->const_price,
                'stock' => $request->stock,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'photo' => $photoPath,
                'expired_at' => $request->expired_at,
            ]);

            //Success response
            return response()->json([
                'message' => 'Product (' . $product->name . ') created successfully',
                'product' => $product
            ], 201);

            // return redirect()->route('products.index')
            //     ->with('success', '${product->name} Product created successfully!');
        } catch (\Exception $e) {
            // Handle errors
            // Rollback photo upload if error
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }


            return response()->json([
                'error' => 'Error creating product: ' . $e->getMessage()
            ], 500);

            return back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return  $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $name = $request->input('name');
        // return response()->json(['name' => $name], 200);
        // return response()->json(['request' => $request->all(), 'id' => $id], 200);
        //is exists sku
        $product = Product::where('sku', $request->sku)->first();
        if ($product && $product->id != $id) {
            return response()->json(['message' => 'This SKU already exists for another product'], 422);
        }

        $validator = Validator::make($request->all(), Product::updatedValidationRules($id));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $photoPath = $product->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');

            // Delete old photo if it exists
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }
        } else {
            $photoPath = $product->photo;
        }

        try {
            $product->update([
                'name' => $request->name,
                // 'sku' => $request->sku, 
                'price' => $request->price,
                'const_price' => $request->const_price,
                'stock' => $request->stock,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'photo' => $photoPath,
                'expired_at' => $request->expired_at,
            ]);

            return response()->json(['message' => 'Product (' . $product->name . ') updated successfully'], 200);
        } catch (\Exception $e) {
            if ($photoPath && $photoPath !== $product->photo) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json(['message' => 'Error updating product: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(string $id)
{
   
    $product = Product::find($id);
    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    
    if (!empty($product->photo) && Storage::disk('public')->exists($product->photo)) {
        Storage::disk('public')->delete($product->photo);
    }

    
    $productName = $product->name;
    $product->delete();

    return response()->json(['message' =>  $productName . ' Product is deleted successfully'], 200);
}



}