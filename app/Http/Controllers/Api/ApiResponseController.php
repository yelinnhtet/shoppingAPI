<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiResponseController extends Controller
{
    public function sendResponse($result , $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'token' => $result->createToken('My App')->accessToken,
            'message' => $message
        ];
       
        return response()->json($response,200);
    }

    public function sendError($error,$errorMessage=[],$code=404)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;
        }
        return response()->json($response,$code);
    }
}
