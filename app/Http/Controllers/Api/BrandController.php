<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{
    public function dataGet()
    {
        $brands = Brand::all();
        return response()->json($brands);
    }

   public function adding(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255|unique:brands,name',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $imagePath = null;
   
    if ($request->hasFile('photo')) {
        $imagePath = $request->file('photo')->store('brands', 'public');
        $validatedData['photo'] = $imagePath;

    } else {
        $validatedData['photo'] = null;
    }

    $brand = Brand::create($validatedData);

    return response()->json([
        'message' => 'Brand created successfully!',
        'brand' => $brand
    ], 200);
}

    public function update(Request $request)
    {
        $brand = Brand::findorfail($request->id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            
            if ($brand->photo) {
                Storage::disk('public')->delete($brand->photo);
            }
            
            $imagePath = $request->file('photo')->store('brands', 'public');
            $validatedData['photo'] = $imagePath;
        } else if ($request->has('clear_photo') && $request->clear_photo == 'true') {
            if ($brand->photo) {
                Storage::disk('public')->delete($brand->photo);
            }
            $validatedData['photo'] = null;
        } else {
            unset($validatedData['photo']);
        }

        $brand->update($validatedData);

        return response()->json([
            'message' => 'Brand updated successfully!',
            'brand' => $brand 
        ], 200);
    }

    public function delete(Request $request)
    {
        $brand = Brand::findorfail($request->id);

        if ($brand->photo) {
            Storage::disk('public')->delete($brand->photo);
        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully!'], 200);
    }
}
