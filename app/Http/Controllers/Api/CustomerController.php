<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use App\Models\Order;

class CustomerController extends Controller
{
    public function index(Request $request)
    {

        $query = Customer::query();
        $customers = $query->when($request->has('name'), function($q) use($request) {
            $q->where('name', 'like', '%'.$request->name.'%');
        })->get();
        // $customers = Customer::query()
        // ->get();
        return response()->json([
            'status' => true,
            'message' => 'Customer with filter',
            'Customer' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $registeredCustomer = Customer::where('email', $request->email)->get();
        if(count($registeredCustomer) > 0) return response()->json(["email" => ["This email is already registered."]]);

        // Store a new custome
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->save();
        if($customer){
            return response()->json([
                'status' => true,
                'message' => 'Create Customer succeffully.',
                'customer' => $customer
            ], 201);
        }else{
            return response()->json(['message' => "Fail to create"]);
        }
    }

    public function show($id)
    {
        $customer = Customer::with('order.payment')->find($id);

        if($customer){
            return response()->json([
                'status' => true,
                'message' => 'Customer detail',
                'customer_detail'=> $customer,
                'customer_orders' => $customer->order,
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                "message" => "User Not Found"
            ], 404);
        }
    }

   public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required|email'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $customer = Customer::find($id);
    if (!$customer) {
        return response()->json([
            'status' => false,
            'message' => 'Customer not found.',
        ], 404);
    }

    $emailExists = Customer::where('email', $request->email)
        ->where('id', '!=', $id) 
        ->exists();

    if ($emailExists) {
        return response()->json([
            'status' => false,
            'message' => 'This email already exists.'
        ], 422);
    }

    $customer->name = $request->name;
    $customer->phone = $request->phone;
    $customer->email = $request->email;
    $customer->save();

    return response()->json([
        'status' => true,
        'message' => 'Update customer successfully.',
        'customer' => $customer
    ]);
}


    public function destroy($id)
    {
        $customer = Customer::find($id);
        if($customer){
            $customer->delete();
            return response()->json([
                'status' => true,
                'message' => "$customer->name is successfully deleted"
            ],200);
        }else{
            return  response()->json([
                'status' => false,
                'message' => "User Not Found"
            ], 404);
        }
    }
}
