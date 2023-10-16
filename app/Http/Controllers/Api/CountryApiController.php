<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Country::all();
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
            'phonecode' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'country_img' => 'required',
            'currency_id' => 'required',
        ]);

        // return $destinationPathCover;

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $data = $request->all();
            $photo = $request->file('country_img');
            // $cover_name = $request->file('country_img')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('country_img')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;

            // return $cover_name;
            $destinationPathCover = public_path() . '/images/country/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }

            $data['country_img'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);
            $country = Country::create($data);

            if ($country) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Country created successfully',
                    'data'=>$country,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create country',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        //
        return $country;
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
    public function update(Request $request, Country $country)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phonecode' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'country_img' => 'required',
            'currency_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {
            $imagePath = public_path("images/country/" . $country->country_img);
            unlink($imagePath);

            $data = $request->all();
            $photo = $request->file('country_img');
            // $cover_name = $request->file('country_img')->getClientOriginalName();
            // $new_photo = time() . $cover_name;

            $orgFileName = $request->file('country_img')->getClientOriginalName();
            $cover_name = str_replace(" ","",$orgFileName);
            $new_photo = time() . $cover_name;
            // return $cover_name;
            $destinationPathCover = public_path() . '/images/country/';

            if (!file_exists($destinationPathCover)) {
                mkdir($destinationPathCover, 0755, true);
            }

            $data['country_img'] = $new_photo;
            $photo->move($destinationPathCover,$new_photo);
            $country->update($data);

            return response()->json(['message' => 'Country updated successfully']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        //
        $country->delete();
        if ($country)
        {
            $imagePath = public_path("images/country/" . $country->country_img);
            unlink($imagePath);
            return response()->json([
                'status' => 200,
                'message' => 'Country delete successfully',
                'data'=>$country,
                ], 200);
            } else
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Country',
                ], 500);
            }
    }

    public function getCountryCode()
    {
        return Country::select('id', 'phonecode')->distinct()->get();
    }
}
