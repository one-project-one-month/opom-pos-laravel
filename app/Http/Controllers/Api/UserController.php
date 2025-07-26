<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\f;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::query();
        $staffList = $query->where('role', 'manager')->get();
        $roleName = $staffList->pluck('role')->unique()->values();
        return response()->json([
            'status' => true,
            'message' => 'All staff list',
            'staff_list' => $staffList,
            'role_name' => $roleName
        ], 200);
    }

    public function suspended($id)
    {
        
        $staff = User::find($id);
        if($staff->role_id != 4){
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
        $staff = User::find($id);
        if($staff->role_id != 4){
            return response()->json([
                'status' => false,
                'message' => 'unauthorized'
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
