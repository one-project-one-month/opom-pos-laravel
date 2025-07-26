<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\f;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Svg\Tag\Rect;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    //     ​ေ
        $manager = Auth::user();

     if(!$manager->hasrole('manager')){
            return response()->json([
                'status' => false,
                'message' => 'unauthorize'
            ], 401);
        }

      $cashiers = User::query()->role('cashier')->get();
      return response()->json([
        'status' => true,
        'message' => 'all cashier user list',
        'Cashier List' => $cashiers
      ], 200);

    }

    public function suspended($id)
    {
        if (!Auth::check()) {
    return response()->json([
        'status' => false,
        'message' => 'Unauthorized (Not Logged In)',
    ], 401);
    }

        
        $staff = User::find($id);
        $manager = Auth::user();
        if(!$manager->hasrole('manager')){
            return response()->json([
                'status' => false,
                'message' => 'unauthorize'
            ], 401);
        }
        if (!$staff) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
         ], 404);
        }
        $staff->suspended = 1;
        $staff->save();
        return response()->json([
            'status' => true,
            'message' => 'This account has been suspended!',
            'staff_acc' => $staff
        ], 200);
    }
     public function unsuspended($id)
    {   
        
        if (!Auth::check()) {
    return response()->json([
        'status' => false,
        'message' => 'Unauthorized (Not Logged In)',
    ], 401);
    }

         $staff = User::find($id);
        $manager = Auth::user();
        if(!$manager->hasrole('manager')){
            return response()->json([
                'status' => false,
                'message' => 'unauthorize'
            ], 401);
        }
        if (!$staff) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        $staff->suspended = 0;
        $staff->save();
        return response()->json([
            'status' => true,
            'message' => 'This account has been unsuspended!',
            'staff_acc' => $staff
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show( $f)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $f)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $f)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $f)
    {
        //
    }
}
