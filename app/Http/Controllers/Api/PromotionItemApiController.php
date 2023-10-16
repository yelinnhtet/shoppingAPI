<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Good;
use App\Models\PromotionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionItemApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $promotionItems = PromotionItem::with('shop','good','promotion')->get();

        return $promotionItems;
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
            'promotion_id' => 'required',
            'shop_id' => 'required',
            'good_id' => 'required',
            'update_price' => 'required',
            'update_qty' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        } else {
            $promotionItem = PromotionItem::create([
                'promotion_id' => $request->input('promotion_id'),
                'shop_id' => $request->input('shop_id'),
                'good_id' => $request->input('good_id'),
                'update_price' => $request->input('update_price'),
                'update_qty' => $request->input('update_qty'),
            ]);
    
            if ($promotionItem) {
                $update_qty=$request->input('update_qty');
                $good_id=$request->input('good_id');

                $good = Good::find($good_id);
                $original_qty=$good->qty;
                $good->qty = $original_qty-$update_qty;
                $good->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Promotion Item created successfully',
                    'data'=>$promotionItem,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to create Promotion Item',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PromotionItem $promotionItem)
    {
        //
        return $promotionItem;
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

    public function update(Request $request, PromotionItem $promotionItem)
    {
        $validator = Validator::make($request->all(), [
            'promotion_id' => 'required',
            'shop_id' => 'required',
            'good_id' => 'required',
            'update_price' => 'required',
            'update_qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }

        $promotionItem->promotion_id = $request->input('promotion_id');
        $promotionItem->shop_id = $request->input('shop_id');
        $promotionItem->good_id = $request->input('good_id');
        $promotionItem->update_price = $request->input('update_price');
        $promotionItem->update_qty = $request->input('update_qty');

        $updated = $promotionItem->save();

        if ($updated) {
            return response()->json([
                'status' => 200,
                'message' => 'Promotion Item updated successfully',
                'data' => $promotionItem,
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update Promotion Item',
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromotionItem $promotionItem)
    {
        //
        $promotionItem->delete();
            if ($promotionItem) {
                return response()->json([
                    'status' => 200,
                    'message' => 'PromotionItem delete successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete PromotionItem',
                ], 500);
            }
    }
}
