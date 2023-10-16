<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $promotions = Promotion::all();
        return $promotions;
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
            'title' => 'required',
            'image_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $promotion = Promotion::create([
                'title' => $request->input('title'),
                'image' => $request->input('image_name'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'type' => $request->input('type'),
            ]);

            if ($promotion) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Promotion created successfully',
                    'data' => $promotion,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Promotion',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        //
        return $promotion;
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

    public function update(Request $request, Promotion $promotion)
    {
        //
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image_name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }

        $promotion = Promotion::find($request->id);

        $promotion->title = $request->input('title');
        $promotion->image = $request->input('image_name');
        $promotion->start_date = $request->input('start_date');
        $promotion->end_date = $request->input('end_date');
        $promotion->status = $request->input('status');
        $promotion->type = $request->input('type');

        if ($request->image_name !== $request->old_image)
            unlink(public_path('images/promotion/' . $request->old_image));

        $updated = $promotion->save();

        if ($updated) {
            return response()->json([
                'status' => 200,
                'message' => 'Promotion updated successfully',
                'data' => $promotion,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update Promotion',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        //
        $promotion->delete();
        if ($promotion) {
            return response()->json([
                'status' => 200,
                'message' => 'Promotion delete successfully',
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete Promotion',
            ], 500);
        }
    }

    public function getPromotionsList(Request $request)
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
            //echo $sortField;exit();
            if (in_array($sortField, array('title', 'start_date', 'end_date',  'type', 'status'))) {
                if ($sortBy == 'desc') {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                } else {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                }
            }
        } else //default sorting
        {
            $sortingName = 'title';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if (count($filterOptions) > 0) {
            for ($i = 0; $i < count($filterOptions); $i++) {
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'title') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'status' || $fieldName == 'start_date' || $fieldName == 'end_date' || $fieldName == 'type') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if (isset($fieldName)) {
            $qryResult = Promotion::select("*", Promotion::raw('(CASE
            WHEN status = "0" THEN "Start"
            WHEN status = "1" THEN "Finished"
            ELSE "Start"
            END) AS status'), Promotion::raw(
                '(CASE
            WHEN type = "1" THEN "Second Sale"
            WHEN type = "2" THEN "Flash Sale"
            ELSE "Second Sale"
            END) AS type'
            ))->where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        } else {
            $qryResult = Promotion::select("*", Promotion::raw(
                '(CASE
            WHEN type = "1" THEN "Second Sale"
            WHEN type = "2" THEN "Flash Sale"
            ELSE "Second Sale"
            END) AS type_name'
            ), Promotion::raw(
                '(CASE
            WHEN status = "0" THEN "Start"
            WHEN status = "1" THEN "Finished"
            ELSE "Start"
            END) AS status_name'
            ))->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $promotionInfo = Promotion::all();
        $dataRowsCount = count($promotionInfo);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/promotion'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }

    public function remove(Request $request)
    {
        $promotion_id = $request->id;
        $result = Promotion::where('id', $promotion_id)->delete();
        if ($result)
            unlink(public_path('images/promotion/' . $request->image));
        return response()->json(['data' => $result]);
    }
}
