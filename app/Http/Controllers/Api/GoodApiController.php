<?php

namespace App\Http\Controllers\Api;
use App\Models\Good;

use App\Models\GoodRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\GoodRepository;
use Illuminate\Support\Facades\Validator;

class GoodApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public $goodRepository;
    public function __construct(GoodRepository $goodRepository)
    {
        $this->goodRepository = $goodRepository;
    }

    public function index()
    {
        //
        return $this->goodRepository->all();
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
        $good = $this->goodRepository->create($request);

        if ($good) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Good created successfully',
                    'data' => $good,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Failed to create Good',
                ],
                500
            );
        }
    }

    public function saveProduct(Request $request)
    {
        //
        $good = $this->goodRepository->create($request);

        if ($good) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Good created successfully',
                    'data' => $good,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Failed to create Good',
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $good = $this->goodRepository->find($id);
        return $good;
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'photo' => 'required',
            'shop_id' => 'required',
            'price' => 'required',
            'discount' => 'required',
            'description' => 'required',
            // 'good_spec_id' => 'required',
            // 'good_para_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->messages(),
                ],
            );
        } else {
            $good = $this->goodRepository->update($request, $id);
            // return $good;
            if ($good) {
                return response()->json(
                    [
                        'status' => 200,
                        'message' => 'Good update successfully',
                        'data' => $good,
                    ],
                );
            } else {
                return response()->json(
                    [
                        'status' => 500,
                        'message' => 'Failed to update Good',
                    ],
                );
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $good = $this->goodRepository->delete($id);
        if ($good) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Good delete successfully',
                    'data' => $good,
                ],
            );
        } else {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Failed to delete Good',
                ],
            );
        }
    }

    public function searchGoodName(Request $request)
    {
        $good = $this->goodRepository->search($request);

        if ($good) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'Good search successfully',
                    'data' => $good,
                ],
            );
        } else {
            return response()->json(
                [
                    'status' => 500,
                    'message' => 'Failed to search Good',
                ],
            );
        }
    }
    public function searchRatingByGoodId()
    {
        $rating = GoodRating::leftJoin(
            'goods',
            'goods.id',
            '=',
            'good_ratings.good_id'
        )
            ->select('good_id', 'rating')
            ->get();

        return $rating;
    }

    public function getGoodsByShopId($shopId)
    {
        $goods = Good::where('shop_id',$shopId)->get();

        return $goods;
    }

    public function getProductList(Request $request)
    {
        $start = $request->gridState['skip'];
        $take = $request->gridState['take'];

        $DisplayStart = 1;
        if(isset($start))
            $DisplayStart = intval($start);

        $DisplayLength = 10;
        if(isset($take))
            $DisplayLength = intval($take);

        $sortingName = '';
        $sortBy = '';
        $sortField = '';

        if(isset($request->gridState['sort'][0]['dir'])) {
            if(count($request->gridState['sort']) > 0) {
                $sort = $request->gridState['sort'][0];
                $sortBy = $sort['dir'] == null ? $sortBy : $sort['dir'];
                $sortField = $sort['field'];
            }
            if(in_array($sortField, array('name','shop_id','price'))) {
                if($sortBy == 'desc'){
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                } else {
                    $sortingName = $sortField;
                    $sortBy = $sortBy;
                }
            }
        }else {
            $sortingName = 'name';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if(count($filterOptions) > 0){
            for($i = 0; $i < count($filterOptions); $i++){
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'name' || $fieldName == 'price') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'shop_id') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if(isset($fieldName)) {
            $qryResult = Good::with('category', 'shop')->where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }else {
            $qryResult = Good::with('category', 'shop')->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $productsWithCountryInfo = Good::all();
        $dataRowsCount = count($productsWithCountryInfo);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/good/goodPhoto'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }

    public function removeProduct(Request $request)
    {
        $good_id = $request->id;
        $result = Good::where('id', $good_id)->delete();

        return response()->json(['data' => $result]);
    }
}
