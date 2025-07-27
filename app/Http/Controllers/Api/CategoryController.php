<?php

namespace App\Http\Controllers\Api;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
     public function index(Request $request)
    {   
       $category = Category::with('product')
                    ->when($request->has('name'), function($q) use ($request){
                        $q->where('name', 'like', '%'.$request->name.'%');
                    })->get();
      $countOfProducts = $category->sum(function($category){
        return $category->product->count();
    });

        return response()->json([
            'status' => true,
            'count of product' => $countOfProducts,
            'category' => $category,
            // 'product' => $productNames
        ], 200);
        
    }

    public function store(Request $request)
    {
       $validatedData = $request->validate([
            'name' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

         if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('categories', 'public');
            $validatedData['photo'] = $imagePath;
        } else {
            $validatedData['photo'] = null;
        }

        $category = Category::create($validatedData);

        return response()->json([
            'message' => 'category created successfully',
            'category' => $category
        ], 201);

       
    }

    public function show($id)
    {

        $category = Category::find($id);

         if(!$category){
             return response()->json(['message' => 'Category not found'], 404);
         }
         return response()->json([
            'status' => true,
            'message' => 'categories detail',
            'category' => $category,
            'category_products' => $category->product
         ]);
       
    }

    public function update(Request $request, Category $category, $id)
    {
         $validator = Validator($request->all(), [
            'name' => 'required',
            'photo' => 'required|image'
         ]);

         if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
         }

         $category = Category::find($id);
         if(!$category){
             return response()->json(['message' => 'Category not found'], 404);
         }

         $photoPath = $category->photo;
         if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('categories', 'public');

        if($category->photo && Storage::disk('public')->exists($category->photo)){
            Storage::disk('public')->delete($category->photo);
         }
        } else {
            $photoPath = $category->photo;
        }
        try {
            $category->update([
                'name' => $request->name,
                'photo' => $photoPath
            ]);
             return response()->json(['message' => 'Category (' . $category->name . ') updated successfully'], 200);
        } catch (\Exception $e) {
              if ($photoPath && $photoPath !== $category->photo) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json(['message' => 'Error updating product: ' . $e->getMessage()], 500);
        }
    }
         

   public function destroy($id)
{
    $category = Category::find($id);
    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    
    if (!empty($product->photo) && Storage::disk('public')->exists($category->photo)) {
        Storage::disk('public')->delete($category->photo);
    }

    
    $categoryName = $category->name;
    $category->delete();

    return response()->json(['message' =>  $categoryName . ' Category is deleted successfully'], 200);

}

}
