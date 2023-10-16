<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // return City::all();
        $citiesWithDetails = City::with('state.country')->get();
        return $citiesWithDetails;
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
            'country_id' => 'required',
            'state_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $city = City::create($request->all());

            if ($city) {
                return response()->json([
                    'status' => 200,
                    'message' => 'City created successfully',
                    'data'=>$city,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create City',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
        return $city;
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
    public function update(Request $request, City $city)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $city->update($request->all());

            return response()->json(['message' => 'City updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        //
        $city->delete();
        if ($city)
        {
            return response()->json([
                'status' => 200,
                'message' => 'City delete successfully',
                'data'=>$city,
                ], 200);
            } else
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete City',
                ], 500);
            }
    }

    public function searchByStateId($state_id){

        $data = City::where('state_id',$state_id)->get();
        return $data;


    }

    public function getCityByID(Request $request){

        $data = City::where('state_id',$request->state_id)->get();
        return response()->json(['data' => $data]);
    }
}
