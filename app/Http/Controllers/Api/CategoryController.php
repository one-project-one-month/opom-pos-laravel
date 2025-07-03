<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    //
    public function adding(Request $request){
        $items = new Category();
        $items->name=$request->name;
        $items->save();

        return response()->json(['Message'=>'Categroy Created Successfuly'],200);
    }

    public function update(Request $request){
        $items = Category::findorfail($request->id);
         $items->name=$request->name;
        $items->update();
        return response()->json(['Message'=>'Categroy Updated Successfuly'],200);
    }

     public function delete(Request $request){
        $items = Category::findorfail($request->id)->delete();
        return response()->json(['Message'=>'Categroy deleted Successfuly'],200);
    }

    public function dataGet(){
        $items=Category::all();
        return response()->json($items);
    }
}
