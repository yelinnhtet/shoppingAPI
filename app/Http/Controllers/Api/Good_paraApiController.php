<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Good_para;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Good_paraApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Good_para::with('currency')->get();
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
            'value' => 'required',
            'currency_id' => 'required',
            'good_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else
        {
            $good_para = Good_para::create($request->all());

            if ($good_para) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Good_para created successfully',
                    'data'=>$good_para,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Good_para',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Good_para $good_para)
    {
        //
        return $good_para;
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
    public function update(Request $request, Good_para $good_para)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'value' => 'required',
            'good_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $good_para->update($request->all());

            return response()->json(['message' => 'Good_para updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Good_para $good_para)
    {
        //
        $good_para->delete();
        if ($good_para)
        {
            return response()->json([
                'status' => 200,
                'message' => 'Good_para delete successfully',
                'data'=>$good_para,
                ], 200);
            } else
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Good_para',
                ], 500);
            }
    }
}
