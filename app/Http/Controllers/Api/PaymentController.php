<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

use function PHPSTORM_META\type;

class PaymentController extends Controller
{

public function index()
{
    $payments = Payment::all();
    return response()->json([
        'status' => true,
        'message' => "all payments method.",
        'payments' => $payments
    ], 200);
}

public function store(Request $request)
{
    $validator = Validator($request->all(),[
        'method' => 'required',
        'photo' => 'required|image'
    ]);
       if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $photoPath = null;
        if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('payments', 'public');
        }
        
        try {
              $payments =Payment::create([
                'method' => $request->method,
                'photo' => $photoPath
            ]);

             return response()->json([
                'message' => 'Product (' . $payments->method . ') created successfully',
                'product' => $payments
            ], 201);
        } catch (\Exception $e) {
        
              if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }


            return response()->json([
                'error' => 'Error creating product: ' . $e->getMessage()
            ], 500);

            return response()->json([
                'status' => false,
                'message' => 'payments create Fail',
                'errors' =>  $e->getMessage()
            ]);
           
        }
      
    }

    public function show(Payment $payment)
    {
        return response()->json([
            'status' => true,
            'message' => 'payment detail',
            '$payment' => $payment
             
        ]);
    }

    public function update( Request $request, $id)
    {
        $validator = Validator($request->all(), [
            'mehtod' => 'required',
            'photo' => 'required|image'
        ]);

        if($validator->fails()){
             return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $payment = Payment::find($id);
        if(!$payment){
             return response()->json(['message' => 'Payment not found'], 404);
        }

        $photoPath = $payment->photo;
        if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('payments', 'public');

            if($payment->photo && Storage::disk('public')->exists($payment->photo)) {
                Storage::disk('public')->delete($payment->photo);
            }
        }else{
            $photoPath = $payment->photo;
        }

        try {
            $payment->update([
                'method' => $request->method,
                'photo' => $photoPath
            ]);
             return response()->json(['message' => 'Payment (' . $payment->method . ') updated successfully'], 200);
        } catch (\Exception $e) {
             if ($photoPath && $photoPath !== $payment->photo) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json(['message' => 'Error updating product: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);
         if (!$payment) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    
    if (!empty($payment->photo) && Storage::disk('public')->exists($payment->photo)) {
        Storage::disk('public')->delete($payment->photo);
    }

    
    $paymentName = $payment->method;
    $payment->delete();

    return response()->json(['message' =>  $paymentName . ' Payment is deleted successfully'], 200);
    }

}
    // public function index()
    // {
    //     $payments = Payment::all();
    //     return response()->json($payments);
    // }

    // public function show(Payment $payment)
    // {
    //     return response()->json($payment);
    // }
;