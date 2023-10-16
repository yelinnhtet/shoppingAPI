<?php

namespace App\Http\Controllers\Api;

use App\Models\Ads_one;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function index()
    {
        $ads = Ads_one::all();
        $ads = count($ads);
        return response()->json(['data' => $ads]);
    }

    public function getAdsList(Request $request)
    {
        $start = $request->gridState['skip'];
        $take = $request->gridState['take'];

        $DisplayStart = 1;
        if (isset($start))
            $DisplayStart = intval($start);

        $DisplayLength = 10;
        if (isset($take))
            $DisplayLength = intval($take);

        $sortingName = '';
        $sortBy = '';
        $sortField = '';

        if (isset($request->gridState['sort'][0]['dir'])) {
            if (count($request->gridState['sort']) > 0) {
                $sort = $request->gridState['sort'][0];
                $sortBy = $sort['dir'] == null ? $sortBy : $sort['dir'];
                $sortField = $sort['field'];
            }
            if (in_array($sortField, array('name', 'image_path'))) {
                if ($sortBy == 'desc') {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                } else {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                }
            }
        } else {
            $sortingName = 'name';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if (count($filterOptions) > 0) {
            for ($i = 0; $i < count($filterOptions); $i++) {
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'name') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'image_path') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if (isset($fieldName)) {
            $qryResult = Ads_one::where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        } else {
            $qryResult = Ads_one::orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $adsWithCountryInfo = Ads_one::all();
        $dataRowsCount = count($adsWithCountryInfo);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function getAdsByMonth(Request $request)
    {
        $month = $request->month;
        $ads = Ads_one::whereMonth('created_at', '=', $month)->get();
        $ads = count($ads);
        return response()->json(['data' => $ads]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'image_name' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages(),
            ], 400);
        } else {
            $ads = Ads_one::create([
                'name' => $request->input('name'),
                'image_path' => $request->input('image_name'),
            ]);
            if ($ads) {
                return response()->json([
                    'status' => 200,
                    'message' => "Ads one Create Successfully",
                    'data' => $ads
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "Failed to create ads one"
                ], 500);
            }
        }
    }

    public function show(Ads_one $ads)
    {
        return $ads;
    }

    public function edit(string $id)
    {
    }

    /* public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages(),
            ], 400);
        } else {
            $ads->name = $request->name;
            $ads->image_path = $request->image_name;
            // $product->update();

            if ($ads) {
                return response()->json([
                    'status' => 200,
                    'message' => "Ads Update Successfully",
                    'data' => $ads
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "Failed to update ads"
                ], 500);
            }
        }
    } */

    public function update(Request $request)
    {
        $ads_one = Ads_one::find($request->id);
        $ads_one->name = $request->name;
        $ads_one->image_path = $request->image_name;

        if ($request->image_name !== $request->old_image)
            unlink(public_path('images/ads/' . $request->old_image));

        $ads_one->save();

        return response()->json(['data' => $ads_one]);
    }

    public function destroy(Ads_one $ads)
    {
        $ads->delete();
        if ($ads) {
            return response()->json([
                'status' => 200,
                'message' => 'Ads delete successfully',
                'data' => true,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete ads',
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('files');
        // print($file);exit();

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images\ads'), $file_name);
            // return $path;
            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }

    public function remove(Request $request)
    {
        $ads_id = $request->id;
        $result = Ads_one::where('id', $ads_id)->delete();
        if ($result)
            unlink(public_path('images/ads/' . $request->image_path));
        return response()->json(['data' => $result]);
    }
}
