<?php

namespace App\Http\Controllers\Api;

use App\Models\ShopRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ShopRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shopRating = ShopRating::with(['user' => function ($query) {
            $query->select('*');},])
        ->with(['shop' => function ($query) {
            $query->select('*');},])
        ->get();

        return $shopRating;
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

            'user_id' => 'required',
            'shop_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $shopRating = ShopRating::create([
                'user_id' => $request->input('user_id'),
                'shop_id' => $request->input('shop_id'),
                'rating' => $request->input('rating'),

            ]);

            if ($shopRating) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Shop Rating created successfully',
                    'data'=> $shopRating,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create shop rating',
                ], 500);
            }
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(ShopRating $shopRating)
    {
        return $shopRating;
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
    public function update(Request $request,ShopRating $shopRating)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            'shop_id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $shopRating->user_id=$request->user_id;
            $shopRating->shop_id=$request->shop_id;
            $shopRating->update();
            if ($shopRating) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Shop Rating edit successfully',
                    'data'=>$shopRating,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit Shop Rating',
                ], 500);
            }

    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShopRating $shopRating)
    {

        $shopRating->delete();
        if ($shopRating) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop rating delete successfully',
                'data'=>$shopRating,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete shop rating',
            ], 500);
        }
    }
}
