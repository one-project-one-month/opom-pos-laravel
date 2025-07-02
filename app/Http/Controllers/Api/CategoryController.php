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
}
