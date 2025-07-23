<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::query()
        ->get();
        return response()->json($customers);
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
            return response()->json($customer);
        }else{
            return response()->json(['message' => "Fail to create"]);
        }
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if($customer){
            return response()->json($customer);
        }else{
            return response()->json([
                "message" => "User Not Found"
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if($customer){
            if($request->name) $customer->name = $request->name;
            if($request->phone) $customer->phone = $request->phone;
            if($request->email) $customer->email = $request->email;
            $customer->save();
            return response()->json($customer);
        }else{
             return response()->json([
                "message" => "User Not Found"
            ]);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if($customer){
            $customer->delete();
            return response()->json([
                'message' => "$customer->name is successfully deleted"
            ]);
        }else{
            return  response()->json([
                'message' => "User Not Found"
            ]);
        }
    }
}
