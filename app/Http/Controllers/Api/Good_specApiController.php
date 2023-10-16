<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Good_spec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Good_specApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Good_spec::all();
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
            'good_id' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $good_spec = Good_spec::create($request->all());
    
            if ($good_spec) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Good_spec created successfully',
                    'data'=>$good_spec,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Good_spec',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Good_spec $good_spec)
    {
        //
        return $good_spec;
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
    public function update(Request $request, Good_spec $good_spec)
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
            $good_spec->update($request->all());

            return response()->json(['message' => 'Good_spec updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Good_spec $good_spec)
    {
        //
        $good_spec->delete();
        if ($good_spec) 
        {
            return response()->json([
                'status' => 200,
                'message' => 'Good_spec delete successfully',
                'data'=>$good_spec,
                ], 200);
            } else 
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Good_spec',
                ], 500);
            }
    }
}
