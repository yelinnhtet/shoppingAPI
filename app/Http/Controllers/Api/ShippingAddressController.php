<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return ShippingAddress::all();
        $addWithCityInfo = ShippingAddress::with('city.state.country.currency')->get();
        return $addWithCityInfo;
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
        $validator = Validator::make($request->all(), [
            'contact'=>'required',
            'user_id'=>'required',
            // 'country_id'=>'required',
            // 'state_id'=>'required',
            // 'city_id'=>'required',
            'phone'=>'required',
            // 'is_default'=>'required',
            'address'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $is_default = $request->input('is_default');

            if ($is_default == 1) 
            {
                $user_id=$request->input('user_id');
                ShippingAddress::where('user_id',$user_id)->where('is_default', '1')->update(['is_default' => '0']);
                $address = ShippingAddress::create([
                    'contact' => $request->input('contact'),
                    'user_id' => $request->input('user_id'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'city_id' => $request->input('city_id'),
                    'phone' => $request->input('phone'),
                    'is_default' => $request->input('is_default'),
                    'address' => $request->input('address'),
                ]);
            }
            else
            {
                $address = ShippingAddress::create([
                    'contact' => $request->input('contact'),
                    'user_id' => $request->input('user_id'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'city_id' => $request->input('city_id'),
                    'phone' => $request->input('phone'),
                    // 'is_default' => $request->input('is_default'),
                    'address' => $request->input('address'),
                ]);
            }
            

            if ($address) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Shipping Address created successfully',
                    'data'=>$address,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Shipping Address',
                ], 500);
            }
        }
    }

    public function shippingAddressByUserId($user_id){

        $data = ShippingAddress::with('city.state.country.currency')
                                ->where('user_id',$user_id)
                                ->get();
        return $data;

    }
    /**
     * Display the specified resource.
     */
    public function show(ShippingAddress $shippingadress)
    {
        return $shippingadress;
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required',
            'user_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'phone' => 'required',
            // 'is_default' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } 
        else {
            $is_default = $request->input('is_default');
            if ($is_default == 1) {
                $user_id=$request->input('user_id');
                ShippingAddress::where('user_id', $user_id)->where('is_default', '1')->update(['is_default' => '0']);
            
                $address = ShippingAddress::find($id);
                if (!$address) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Shipping Address not found',
                    ], 404);
                }

                $address->update([
                    'contact' => $request->input('contact'),
                    'user_id' => $request->input('user_id'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'city_id' => $request->input('city_id'),
                    'phone' => $request->input('phone'),
                    'is_default' => $is_default,
                    'address' => $request->input('address'),
                ]);
            }
            else{
                $address = ShippingAddress::find($id);

                if (!$address) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Shipping Address not found',
                    ], 404);
                }

                $address->update([
                    'contact' => $request->input('contact'),
                    'user_id' => $request->input('user_id'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'city_id' => $request->input('city_id'),
                    'phone' => $request->input('phone'),
                    'address' => $request->input('address'),
                ]);
            }            

            return response()->json([
                'status' => 200,
                'message' => 'Shipping Address updated successfully',
                'data' => $address,
            ], 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingAddress $shippingadress)
    {
        $shippingadress->delete();
            if ($shippingadress) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Shipping Address delete successfully',
                    'data'=>$shippingadress,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Shipping Address',
                ], 500);
            }
    }
}