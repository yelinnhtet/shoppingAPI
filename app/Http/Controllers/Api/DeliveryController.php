<?php

namespace App\Http\Controllers\Api;

use App\Models\Delivery;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Delivery::all();
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
            'logo'=>"required",
            'name'=>"required"
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ],400);
        }else{
            $data = $request->all();
            $photo = $request->file('logo');
            // $cover_name = $request->file('logo')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('logo')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;

            // return $cover_name;
            $destinationPathCover = public_path() . '/images/delivery/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }
            $data['logo'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);
            $deliver = Delivery::create($data);

            if ($deliver) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Delivery created successfully',
                    'data'=>$deliver,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Delivery',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

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
    public function update(Request $request, Delivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $imagePath = public_path("images/delivery/" . $delivery->logo);
            unlink($imagePath);

            $data = $request->all();
            $photo = $request->file('logo');
            // $cover_name = $request->file('logo')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('logo')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;
            // return $cover_name;
            $destinationPathCover = public_path() . '/images/delivery/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }

            $data['logo'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);
            $delivery->update($data);

            return response()->json(['message' => 'Delivery updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();
        if ($delivery)
        {
            $imagePath = public_path("images/delivery/" . $delivery->logo);
            unlink($imagePath);
            return response()->json([
                'status' => 200,
                'message' => 'deliver delete successfully',
                'data'=>$delivery,
                ], 200);
            } else
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Delivery',
                ], 500);
            }
    }
}
