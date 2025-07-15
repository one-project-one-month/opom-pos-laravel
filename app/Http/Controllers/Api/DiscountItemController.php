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
        $discounts = DiscountItem::with('products')->get();
        return response()->json($discounts);
    }


    public function store(Request $request)
    {
        if ($request->title == null) {
            return response()->json([
                'message' => 'Title is required.',
                'errors' => [
                    'title' => 'The title field is required.'
                ]
            ], 422);
        }
        if ($request->dis_percent == null) {
            return response()->json([
                'message' => 'Discount percentage is required.',
                'errors' => [
                    'dis_percent' => 'The discount percentage field is required.'
                ]
            ], 422);
        }
        if ($request->dis_percent < 0 || $request->dis_percent > 100) {
            return response()->json([
                'message' => 'Discount percentage must be between 0 and 100.',
            ], 422);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'dis_percent' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
 
      
        $discount = DiscountItem::create($validated);

        return response()->json([
            'message' => 'Discount item created successfully.',
            'data' => $discount
        ], 201);
    }

    public function update(Request $request, DiscountItem $discountItem)
    {
        $isExits = DiscountItem::where('id', $discountItem->id)->exists();
        if (!$isExits) {
            return response()->json([
                'message' => 'Discount item not found.',
                'errors' => [
                    'id' => 'The specified discount item does not exist.'
                ]
            ], 404);
        }
        if ($request->title == null) {
            return response()->json([
                'message' => 'Title is required.',
                'errors' => [
                    'title' => 'The title field is required.'
                ]
            ], 422);
        }
        if ($request->dis_percent == null) {
            return response()->json([
                'message' => 'Discount percentage is required.',
                'errors' => [
                    'dis_percent' => 'The discount percentage field is required.'
                ]
            ], 422);
        }
        if ($request->dis_percent < 0 || $request->dis_percent > 100) {
            return response()->json([
                'message' => 'Discount percentage must be between 0 and 100.',
            ], 422);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'dis_percent' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $discountItem->update($validated);

        return response()->json([
            'message' => 'Discount item updated successfully.',
            'data' => $discountItem
        ]);
    }
    public function show($id)
    {
        $discountItem = DiscountItem::with('products')->findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => "DiscountItem Detail",
            'discountItem' => $discountItem
        ], 200);
    
    }

    public function destroy($id)
    {
        $discountItem = DiscountItem::findOrFail($id);
        $discountItem->delete();

        return response()->json([
            'message' => 'DiscountItem has been deleted successfully.',
        ],200);
    }



    public function productAddToDiscount(Request $request)
    {
       
        if(!$request->discount_id) {
            return response()->json([
                'status' => false,
                'message' => "Discount_id is required."
            ], 422);
        }


        if(!$request->product_ids) {
            return response()->json([
                'status' => false,
                'message' => "Product_ids is required."
            ], 422);
        }

        if(!($request->discount_id && $request->product_ids) ){
            return response()->json([
                'status' =>  false,
                'message' => "Discount_id is required and Product_ids is required"
             ], 422);
        }


        $validated = $request->validate([
            'discount_id' => 'required|exists:discount_items,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $discountId = $validated['discount_id'];
        $productIds = $validated['product_ids'];

        Product::whereIn('id', $productIds)
            ->update(['discount_item_id' => $discountId]);

        return response()->json([
            'message' => 'Products added to discount successfully.',
            'discount_id' => $discountId,
            'product_ids' => $productIds,
        ]);
    }

    public function discountedProductUpdate(Request $request, $discountId)
    {
        if(!$request->product_ids) {
            return response()->json([
                "status" => false,
                "message" => "product_ids is required."     
            ], 422);
        }

        if(!$request->discount_id) {
            return response()->json([
                "status" => false,
                "message" => "discount_id is required."
            ], 422);
        }

        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        Product::where('discount_item_id', $discountId)
            ->whereNotIn('id', $validated['product_ids'])
            ->update(['discount_item_id' => null]);

        Product::whereIn('id', $validated['product_ids'])
            ->update(['discount_item_id' => $discountId]);

        return response()->json([
            'message' => 'Discounted products updated successfully.',
            'discount_id' => $discountId,
            'product_ids' => $validated['product_ids'],
        ]);
    }
}
