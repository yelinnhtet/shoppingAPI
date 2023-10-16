<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ShopRepository;
use Defuse\Crypto\File;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $shopRepository;
        public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function index()
    {
        $shops = Shop::all();
        $shops = count($shops);
        return response()->json(['data' => $shops]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'agent_user_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'name' => 'required',
            'logo' => 'required',
            'cover_photo' => 'required',
            'shopkeeper_nrc' => 'required',
            'nrc' => 'required',
            'address' => 'required',
        ]);
    if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'errors' => $validator->messages(),
        ], 400);
    } else {
        $shop = $this->shopRepository->create($request);

        $user_id=$request->user_id;
        $user = User::find($user_id);
        if ($user->role === 'User') {
            $user->role = 'Merchant';
            $user->save();
        }

        if ($shop) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop created successfully',
                'data'=>$shop,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create shop',
            ], 500);
        }
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $shop=$this->shopRepository->find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'agent_user_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'name' => 'required',
            'logo' => 'required',
            'cover_photo' => 'required',
            'shopkeeper_nrc' => 'required',
            'nrc' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
        $shop = $this->shopRepository->update($request,$id);
        return $shop;
        if ($shop) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop update successfully',
                'data'=>$shop,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update shop',
            ], 500);
        }
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $shop = $this->shopRepository->delete($id);
        if ($shop) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop delete successfully',
                'data'=>$shop,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete shop',
            ], 500);
        }
    }
    public function shopLimit()
    {

        // $limit = Shop::orderBy('created_at', 'desc')->limit(4)->get();
        $limit = Shop::orderBy('status', 'asc')->limit(4)->get();

        if ($limit) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop List',
                'data'=>$limit,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to search Shop',
            ], 500);
        }
    }

    public function shopListByUserId($id)
    {
        $shopListByUserId = Shop::with('user','cities','states','countries')->where("user_id", $id)->orderBy('status', 'asc')
        ->get();
        if ($shopListByUserId) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop List',
                'data'=>$shopListByUserId,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to search Shop',
            ], 500);
        }
    }

    public function search(Request $request)
    {    $request->validate([
        'name' => 'required|string',
    ]);
        $name = $request->input('name');
        $shop = Shop::where('name', 'like', '%' . $name . '%')->get();
        return $shop;


        if ($shop) {
            return response()->json([
                'status' => 200,
                'message' => 'Shop search successfully',
                'data'=>$shop,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to search Shop',
            ], 500);
        }
    }

    public function getShopList(Request $request)
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
            if (in_array($sortField, array('name', 'nrc', 'address'))) {
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
            $sortingName = 'name';
            $sortBy = 'asc';
        }

        $filterOptions = $request->gridState['filter']['filters'];
        if (count($filterOptions) > 0) {
            for ($i = 0; $i < count($filterOptions); $i++) {
                $fieldName = $filterOptions[$i]['field'];
                $fieldvalue = $filterOptions[$i]['value'];
                $FilterBy = '';

                if ($fieldName == 'name' || $fieldName == 'address') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'nrc') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        $qryResult = null;

        if (isset($fieldName)) {
            $qryResult = Shop::where($fieldName, $FilterBy, $fieldvalue)
                ->orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        } else {
            $qryResult = Shop::orderBy($sortingName, $sortBy)
                ->skip($DisplayStart)
                ->take($DisplayLength)
                ->get();
        }

        $shopsWithCountryInfo = Shop::all();
        $dataRowsCount = count($shopsWithCountryInfo);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function getShop(){
        $res_shop = Shop::all();
        return response()->json(['data'=> $res_shop]);
    }

    public function logo(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/shop/logo'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }
    public function coverPhoto(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/shop/coverPhoto'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }
    public function shopkeeperNrc(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/shop/shopkeeperNrc'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }
}
