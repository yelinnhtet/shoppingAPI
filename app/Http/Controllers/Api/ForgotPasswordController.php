<?php

namespace App\Http\Controllers\Api;

use Hash;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function searchByEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => "Email is required!",
            ], 400);
        }
        else{
            $email = $request->input('email');
            $result = User::where('email',$email)->first();


            if ($result) {
                Cache::add('usermail', $result);
                return response()->json([
                    'status' => 200,
                    'message' => 'your email is correct!',
                    'data'=>$result,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Your email not found!',
                ], 500);
            }
        }
    }

    public function resetPwd(Request $request){
        $userData = Cache::get('usermail');
        $email = $userData->email;
        $validator = Validator::make($request->all(), [

            'password' => 'required',
            'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }
        else {

            $password = $request->input('password');
            $confirm_password = $request->input('confirm_password');
            $old_password = User::where('email', $email)->first()->password;

            if(Hash::check($password, $old_password)){
                return response()->json([
                    'status' => 500,
                    'message' =>'Your old password and new password is same!',
                ], 500);
            }
            if($password == $confirm_password){
                $user = User::where('email', $email)
                  ->update(['password' => Hash::make($password)]);
                  return response()->json([
                    'status' => '200',
                    'message' => 'Password updated successfully!'],200);
            }else{

                return response()->json([
                    'status' => 500,
                    'message' =>'Password and Confirm Password must same!',
                ], 500);

            }

        }

    }
}
