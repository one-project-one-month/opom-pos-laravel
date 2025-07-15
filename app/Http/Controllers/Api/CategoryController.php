<?php

namespace App\Http\Controllers\Api;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Product;

class CategoryController extends Controller
{
    //
    // public function adding(Request $request){
    //     $items = new Category();
    //     $items->name=$request->name;
    //     $items->save();

    //     return response()->json(['Message'=>'Categroy Created Successfuly'],200);
    // }

    // public function update(Request $request){
    //     $items = Category::findorfail($request->id);
    //      $items->name=$request->name;
    //     $items->update();
    //     return response()->json(['Message'=>'Categroy Updated Successfuly'],200);
    // }

    //  public function delete(Request $request){
    //     $items = Category::findorfail($request->id)->delete();
    //     return response()->json(['Message'=>'Categroy deleted Successfuly'],200);
    // }

    // public function dataGet(){
    //     $items=Category::all();
    //     return response()->json($items);
    // }
     public function index(Request $request)
    {   
       $category = Category::with('product')
                    ->when($request->has('name'), function($q) use ($request){
                        $q->where('name', 'like', '%'.$request->name.'%');
                    })->get();

        return response()->json([
            'status' => true,
            'category' => $category,
            // 'product' => $productNames
        ], 200);
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return new CategoryResource($category);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);

        return new CategoryResource($category);
    }

   public function destroy($id)
{
    $category = Category::findOrFail($id);
    $category->delete();
    return response()->json(['message' => 'Category deleted']);
}

}
