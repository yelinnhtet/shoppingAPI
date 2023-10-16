<?php

namespace App\Http\Controllers\Api;

use App\Models\RatingUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RatingUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return RatingUser::all();
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
            'good_id' => 'required',
            'shop_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $ratingUser = RatingUser::create([
                'user_id' => $request->input('user_id'),
                'good_id' => $request->input('good_id'),
                'shop_id' => $request->input('shop_id'),
                'goodrating' => $request->input('goodrating'),
                'shoprating' => $request->input('shoprating')
            ]);

            if ($ratingUser) {
                return response()->json([
                    'status' => 200,
                    'message' => 'State created successfully',
                    'data'=>$ratingUser,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create state',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RatingUser $ratingUser)
    {
        return $ratingUser;
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RatingUser $ratingUser)
    {

    }
}
