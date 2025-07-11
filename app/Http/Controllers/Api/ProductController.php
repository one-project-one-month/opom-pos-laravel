<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  Product::all();
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
            return redirect()->route('products.index')
                ->with('success', '${product->name} Product created successfully!');
        } catch (\Exception $e) {
            // Handle errors
            // Rollback photo upload if error
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

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
                'sku' => $request->sku,
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
        if (!Product::find($id)) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        if (Storage::disk('public')->exists(Product::find($id)->photo)) {
            Storage::disk('public')->delete(Product::find($id)->photo);
        }

        $Product = Product::find($id);
        $Product->delete();
        return response()->json(['message' =>  $Product->name . ' Product is deleted successfully'], 200);
    }
}