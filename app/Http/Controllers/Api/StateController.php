<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\String_;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return State::all();
        $statesWithCountryInfo = State::with('country')->get();
        return $statesWithCountryInfo;
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
            'name' => 'required',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $state = State::create([
                'name' => $request->input('name'),
                'country_id' => $request->input('country_id'),
            ]);

            if ($state) {
                return response()->json([
                    'status' => 200,
                    'message' => 'State created successfully',
                    'data'=>$state,
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
    public function show(State $state)
    {
        return $state;
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
    public function update(Request $request, State $state)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
                $state->name=$request->name;
                $state->country_id=$request->country_id;
                $state->update();
            if ($state) {
                return response()->json([
                    'status' => 200,
                    'message' => 'State edit successfully',
                    'data'=>$state,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit state',
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        // $state=State::find($id);
        $state->delete();
            if ($state) {
                return response()->json([
                    'status' => 200,
                    'message' => 'State delete successfully',
                    'data'=>$state,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete state',
                ], 500);
            }
    }


    public function searchByCountryId($country_id){

        $data = State::where('country_id',$country_id)
                        ->get();
        return $data;

    }

    public function getStateByID(Request $request){

        $data = State::where('country_id',$request->country_id)->get();
        return response()->json(['data' => $data]);
    }
}
