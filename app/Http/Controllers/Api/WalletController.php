<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return Wallet::all();
        $walletWithInfo = Wallet::with('user')->get();
        return $walletWithInfo;
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
            // return($request->input('payment_id'));exit();
            $state = Wallet::create([
                'user_id' => $request->input('user_id'),
                'amount' => $request->input('amount'),
                'payment_id' => $request->input('payment_id'),
            ]);
    
            if ($state) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Wallet created successfully',
                    'data'=>$state,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Wallet',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        return $wallet;
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
    public function update(Request $request, Wallet $wallet)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
                $wallet->user_id=$request->user_id;
                $wallet->amount=$request->amount;
                $wallet->update();
            if ($wallet) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Wallet edit successfully',
                    'data'=>$wallet,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to edit Wallet',
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        $wallet->delete();
            if ($wallet) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Wallet delete successfully',
                    'data'=>$wallet,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete Wallet',
                ], 500);
            }
    }

    public function getBalanceByUserId($id)
    {
        $wallet = Wallet::with('user')->where('user_id', $id)->where('status',1)->pluck('amount');
        $withdraw = Withdraw::with('user')->where('user_id', $id)->where('status',1)->pluck('amount');

        // if ($wallet->isEmpty() && $withdraw->isEmpty()) {
        //     return response()->json([
        //         'status' => 500,
        //         'message' => 'Fail'
        //     ], 500);
        // }

        $totalAmount = $wallet->sum() - $withdraw->sum();
        $formattedTotalAmount = number_format($totalAmount);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $formattedTotalAmount,
        ], 200);

    }

    public function getWalletHistoryByUserId($id)
    {
        $walletHistory=Wallet::with('user')->where('user_id',$id)->get();
        if ($walletHistory) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data'=>$walletHistory,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Fail',
            ], 500);
        }
    }
}