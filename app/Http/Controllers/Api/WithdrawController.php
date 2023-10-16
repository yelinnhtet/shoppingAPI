<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Support\Facades\Validator;

class WithdrawController extends Controller
{
    function index() {

        $withdraws = Withdraw::with('user')->get();
        return $withdraws;
        
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
            'payment_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } 
        else 
        {
            $wallet = Wallet::with('user')->where('user_id', $request->user_id)->where('status', 1)->pluck('amount');
            $withdraw = Withdraw::with('user')->where('user_id', $request->user_id)->where('status', 1)->pluck('amount');
            $totalAmount = $wallet->sum() - $withdraw->sum();
        
            $success = 0;
        
            if (($request->amount) < $totalAmount) {
                $withdrawAmount = Withdraw::create([
                    'user_id' => $request->input('user_id'),
                    'amount' => $request->input('amount'),
                    'payment_id' => $request->input('payment_id'),
                ]);
                $success=1;
            }
        
            if ($success==1) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Withdraw created successfully',
                    'data' => $withdrawAmount,
                ], 200);
            } 
            else 
            {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create withdraw',
                ], 500);
            }
        }
    
    }

    public function show(Withdraw $withdraw)
    {
        return $withdraw;
    }

    public function update(Request $request, Withdraw $withdraw)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
            'payment_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
                $withdraw->user_id=$request->user_id;
                $withdraw->amount=$request->amount;
                $withdraw->payment_id=$request->payment_id;
                $withdraw->update();
            if ($withdraw) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Withdraw edit successfully',
                    'data'=>$withdraw,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit withdraw',
                ], 500);
            }
        }
    }

    public function destroy(Withdraw $withdraw)
    {
        
        $withdraw->delete();
            if ($withdraw) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Withdraw delete successfully',
                    'data'=>$withdraw,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete withdraw',
                ], 500);
            }
    }

    public function getWithdrawHistoryByUserId($id)
    {
        $withdrawHistory=Withdraw::with('user')->where('user_id',$id)->get();
        if ($withdrawHistory) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data'=>$withdrawHistory,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Fail',
            ], 500);
        }
    }
}
