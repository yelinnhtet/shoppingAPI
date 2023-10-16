<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $UserRepo;
    public $Response;
    public function __construct(UserRepo $UserRepo, ApiResponseController $Response)
    {
        $this->UserRepo = $UserRepo;
        $this->Response = $Response;
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:255',
            'phone' => 'required|numeric',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Fill all feilds',
            ], 400);
        } else {
            $user = $this->UserRepo->create($request);
            if ($user) {
                return $this->Response->sendResponse($user, 'SuccessFully');
            } else {
                return $this->Response->sendError("Cannt Register");
            }
        }
    }

    public function login(Request $request)
    {
        /* $user = $this->UserRepo->checkUser($request);

        if(!empty($user['data']['error'])){
            return $this->Response->sendError($user['data']['error']);
        }
        return $this->Response->sendResponse($user['data']['user'],'SuccessFully'); */
        $user = $this->UserRepo->checkUser($request);

        if (!empty($user['data']['error'])) {
            return $this->Response->sendError($user['data']['error']);
        } else
            $user['data']['user']['token'] = $user['data']['user']->createToken('My App')->accessToken;
        return response()->json($user['data']['user']);
    }

    public function index()
    {
        $users = User::all();
        $users = count($users);
        return response()->json(['data' => $users]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            // 'photo' => 'required',
            'email' => 'required',
            // 'password' => 'required',
            'phone' => 'required',
            // 'lat' => 'required',
            // 'long' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $user = $this->UserRepo->update($request, $id);
            // return $user;
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'message' => 'User update successfully',
                    'data' => $user,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to update User',
                ], 500);
            }
        }
    }

    public function destroy(string $id)
    {
        $user = $this->UserRepo->delete($id);
        if ($user) {
            return response()->json([
                'status' => 200,
                'message' => 'User delete successfully',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete User',
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
        $user = $this->UserRepo->find($id);
        return $user;
    }

    public function getAuthUser($id)
    {
        //
        $user = $this->UserRepo->find($id);
        return $user;
    }

    public function getUserList(Request $request)
    {
        $u_id = $request->u_id;
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
            if (in_array($sortField, array('name', 'email', 'role'))) {
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

                if ($fieldName == 'name' || $fieldName == 'email') {
                    $fieldName = $fieldName;
                    $fieldvalue = '%' . $fieldvalue . '%';
                    $FilterBy = 'LIKE';
                }
                if ($fieldName == 'role') {
                    $fieldName = $fieldName;
                    $fieldvalue = $fieldvalue;
                    $FilterBy = '=';
                }
            }
        }

        if ($u_id == 1) {
            $qryResult = User::where('role', 'user');
            if (isset($fieldName)) {
                $qryResult = $qryResult->where($fieldName, $FilterBy, $fieldvalue);
            }
            $usersWithCountryInfo = User::where('role', 'user')->get();
        } else if ($u_id == 2) {
            $qryResult = User::where('role', 'Admin');
            if (isset($fieldName)) {
                $qryResult = $qryResult->where($fieldName, $FilterBy, $fieldvalue);
            }
            $usersWithCountryInfo = User::where('role', 'Admin')->get();
        } else if ($u_id == 3) {
            $qryResult = User::where('role', 'Agent');
            if (isset($fieldName)) {
                $qryResult = $qryResult->where($fieldName, $FilterBy, $fieldvalue);
            }
            $usersWithCountryInfo = User::where('role', 'Agent')->get();
        } else if ($u_id == 4) {
            $qryResult = User::where('role', 'Merchant');
            if (isset($fieldName)) {
                $qryResult = $qryResult->where($fieldName, $FilterBy, $fieldvalue);
            }
            $usersWithCountryInfo = User::where('role', 'Merchant')->get();
        } else if ($u_id == 5) {
            $qryResult = User::where('role', 'Supervisor');
            if (isset($fieldName)) {
                $qryResult = $qryResult->where($fieldName, $FilterBy, $fieldvalue);
            }
            $usersWithCountryInfo = User::where('role', 'Supervisor')->get();
        }

        $qryResult = $qryResult->orderBy($sortingName, $sortBy)
            ->skip($DisplayStart)
            ->take($DisplayLength)
            ->get();

        $usersWithCountryInfo = User::where('role', 'user')->get();
        $dataRowsCount = count($usersWithCountryInfo);
        return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }

    public function changeStatus(Request $request)
    {
        $user_id = $request->id;
        $status = $request->status;
        $affected = User::where('id', $user_id)
            ->update(['status' => $status]);
        return response()->json(['data' => $affected]);
    }

    public function remove(Request $request)
    {
        $user_id = $request->id;
        $result = User::where('id', $user_id)->delete();
        return response()->json(['data' => $result]);
    }

    public function uploadPhoto(Request $request)
    {
        $file = $request->file('files');

        if ($file) {
            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $file_name = uniqid() . $subFileName;
            $path = $file->move(public_path('images/user'), $file_name);

            return response()->json(['data' => ["path" => str($path), "image_name" => $file_name]]);
        }
    }
}
