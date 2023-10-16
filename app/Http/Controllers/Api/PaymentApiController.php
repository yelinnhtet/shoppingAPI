<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Payment::all();
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'logo' => 'required',
            'currency_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else
        {
            $data = $request->all();
            $photo = $request->file('logo');
            // $cover_name = $request->file('logo')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('logo')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;

            // return $cover_name;
            $destinationPathCover = public_path() . '/images/payment/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }
            $data['logo'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);

            $payment = Payment::create($data);

            if ($payment) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Payment created successfully',
                    'data'=>$payment,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Payment',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'logo' => 'required',
            'currency_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $imagePath = public_path("images/payment/" . $payment->logo);
            unlink($imagePath);

            $data = $request->all();
            $photo = $request->file('logo');
            // $cover_name = $request->file('logo')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('logo')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;
            $destinationPathCover = public_path() . '/images/payment/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }
            $data['logo'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);

            $payment->update($data);

            return response()->json(['message' => 'Payment updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
        $payment->delete();
        if ($payment)
        {
            $imagePath = public_path("images/payment/" . $payment->logo);
            unlink($imagePath);
            return response()->json([
                'status' => 200,
                'message' => 'Payment delete successfully',
                'data'=>$payment,
                ], 200);
            } else
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Payment',
                ], 500);
            }
    }
}
