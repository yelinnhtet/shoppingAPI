<?php

namespace App\Http\Controllers\Api;

use App\Models\GoodRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GoodRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $goodRating = GoodRating::with(['user' => function ($query) {
        //     $query->select('*');},])
        // ->with(['good' => function ($query) {
        //     $query->select('*');},])
        // ->get();
        $goodRating=GoodRating::get();
        return $goodRating;
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
        // return $request->rating;exit();
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            'good_id' => 'required',

        ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 400,
        //         'errors' => $validator->messages(),
        //     ], 400);
        // } else {
            $goodR = GoodRating::all();

        foreach ($goodR as $item) {
            if ($item->user_id == $request->userId && $item->good_id == $request->goodId) {
                // Update the existing record
                $item->rating = $request->rating;
                $item->save();
                return $item; // Return the updated record
            }
        }

// If no matching record was found, create a new one
        $goodRating = GoodRating::create([
            'user_id' => $request->userId,
            'good_id' => $request->goodId,
            'rating' => $request->rating,
        ]);

        return $goodRating; 
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodRating $goodRating)
    {
        return $goodRating;
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
    public function update(Request $request,GoodRating $goodRating)
    {

// return $goodRating;
        $validator = Validator::make($request->all(), [

            'user_id' => 'required',
            'good_id' => 'required',

        ]);
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 400,
        //         'errors' => $validator->messages(),
        //     ], 400);
        // } else {
                $goodRating->user_id=$request->user_id;
                $goodRating->good_id=$request->good_id;
                $goodRating->update();
            if ($goodRating) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Good Rating edit successfully',
                    'data'=>$goodRating,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit Good Rating',
                ], 500);
            }
        // }








    //     $goodRating = GoodRating::find($id);
    //     $goodRating->update($request->all());
    // if ($goodRating) {
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Good Rating edit successfully',
    //         'data'=>$goodRating,
    //     ], 200);
    // } else {
    //     return response()->json([
    //         'status' => 500,
    //         'message' => 'Failed to edit good rating',
    //     ], 500);
    // }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodRating $goodRating)
    {
        $goodRating->delete();
        if ($goodRating) {
            return response()->json([
                'status' => 200,
                'message' => 'Good rating delete successfully',
                'data'=>$goodRating,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete good rating',
            ], 500);
        }
    }
    public function searchRatingByGoodId($id)
    {
        $goodRating=GoodRating::where('good_id',$id)->first();
    return $goodRating;
    }
}
