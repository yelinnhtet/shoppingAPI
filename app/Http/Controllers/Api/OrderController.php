<?php

namespace App\Http\Controllers\Api;

use App\Models\cart;
use App\Models\Good;
use App\Models\Shop;
use App\Models\Order;
use App\Helper\Helper;
use App\Models\Wallet;
use App\Models\Good_para;
use App\Models\Good_order;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use function Laravel\Prompts\alert;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();
        $orders = count($orders);
        return response()->json(['data' => $orders]);
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
        // $this->generateOrderInvoiceId('W'); Invoice Number Function
        $orderData = $request->all();
        $name=(string)$request[0]['user']['name'];
        $balance=Wallet::where('user_id',$request[0]['user_id'])->first();
        $orderArr = [];
        $delCart=[];
        $cartData=[];
        foreach($orderData as $item){
            array_push($orderArr,$item);
        }
        foreach($orderArr as $id){
            $i=0;
            if($i<count($orderArr)){

                $carts=cart::with('good_paras','good')->where('id',$id)->get();
                $goodParaId=$carts[$i]['good_paras_id'];
                $goodPara=Good_para::with('cart')->where('id',$goodParaId)->first();
                $goodQty=$goodPara->value-$carts[$i]['quantity'];
                // return $goodPara->value;exit();
                if($balance['amount']>=$carts[$i]['totalPrice']){
                $goodParas=Good_para::findorFail($goodParaId);
                $goodParas->id=$carts[$i]['good_paras'][$i]->id;
                $goodParas->name=$carts[$i]['good_paras'][$i]->name;
                $goodParas->value=$goodQty;
                $goodParas->currency_id=$carts[$i]['good_paras'][$i]->currency_id;
                $goodParas->good_id=$carts[$i]['good_paras'][$i]->good_id;
                $goodParas->update();
                $goodId=$carts[$i]['good']['id'];
                $good=Good::where('id',$goodId)->get();
                $goods=Good::where('id',$goodId)->first();
                $updateQty=$good[$i]['qty']-$carts[$i]['quantity'];
                $goods->id= $carts[$i]['good']->id;
                $goods->name=$carts[$i]['good']->name;
                $goods->photo=$carts[$i]['good']->photo;
                $goods->shop_id=$carts[$i]['good']->shop_id;
                $goods->category_id=$carts[$i]['good']->category_id;
                $goods->price=$carts[$i]['good']->price;
                $goods->discount=$carts[$i]['good']->discount;
                $goods->qty=$updateQty;
                $goods->description=$carts[$i]['good']->description;
                $goods->save();
                $order=Order::create([
                    'order_number'=>$this->generateOrderInvoiceId(substr($name,0,1)),
                    'user_id' => $request[0]['user_id'],
                    'good_id'=>$carts[$i]['good_id'],
                    'good_paras_id'=>$carts[$i]['good_paras_id'],
                    'good_specs_id'=>$carts[$i]['good_specs_id'],
                    'quantity'=>$carts[$i]['quantity'],
                    'totalPrice'=>$carts[$i]['totalPrice']
                ]);

                $balance->amount=$balance['amount']-$carts[$i]['totalPrice'];
                $balance->update();
                cart::with('good_paras','good_specs')->where('id',$id)->delete();
                $i++;
                }
                else{
                    return response()->json([
                        'status'=>500,
                        'message'=>"Your balance is Insufficient"
                    ],500);
                }

            }

            }


            // return $b;
    //  $dataCount = count($data);
    //  for($i=0; $i<$dataCount; $i++){
    //     echo $data[$i]['id'];
    //  }
    //    return $req;exit();
        // return $request[0]['id'];exit();
        // $data=$request->json();
        // $order=Order::get();
            // $cart->where($request);
            // return $cart;exit();
        // }
        // $shop=Shop::get();
        // $orderData=
        // SELECT * FROM orders RIGHT JOIN carts ON carts.user_id = orders.user_id RIGHT JOIN goods ON carts.good_id = goods.id RIGHT JOIN shops ON shops.id = goods.shop_id WHERE shops.id=1 GROUP BY shops.id,goods.id;
        // return $cart;exit();
        // return ( $request[0]['user_id']);exit();
        // $validator=Validator::make($request->all(),[
        //     'order_number'=>'required',
        //     'user_id' => 'required',

        //     'goods' => 'required'
        // ]);
        // if($validator->fails()){
        //     return response()->json([
        //         'status'=>400,
        //         'error'=>$validator->messages(),
        //     ],400);
        // }else{
            // $order=Order::create([
            //     'order_number'=>$this->generateOrderInvoiceId($request[0]['user']['name']),
            //     'user_id' => $request[0]['user_id'],
            // ]);

            // $orderData=Good_para::where('id',$cart->good_paras_id)->select('value')->get();
// return $request[0]['good'];exit();
            // $orderId = $order->id;
            // return($orderId);



            // if($order){
            //     return response()->json([
            //         'status'=>200,
            //         'message'=>"Order Create Successfully",
            //         'data'=>$order
            //     ]);
            // }else{
                // return response()->json([
                //     'status'=>500,
                //     'message'=>"Failed to create order"
                // ],500);
            // }
        // }
    }
// }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $order;
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
    public function update(Request $request, Order $order)
    {
        $validator=Validator::make($request->all(),[
            'order_number'=>'required',
            'user_id'=>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>400,
                'error'=>$validator->messages(),
            ],400);
        }else{
            $order->order_number=$request->order_number;
            $order->user_id=$request->user_id;
            $order->update();

            if($order){
                return response()->json([
                    'status'=>200,
                    'message'=>"Order Update Successfully",
                    'data'=>$order
                ]);
            }else{
                return response()->json([
                    'status'=>500,
                    'message'=>"Failed to update order"
                ],500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
            if ($order) {
                return response()->json([
                    'status' => 200,
                    'message' => 'order delete successfully',
                    'data'=>$order,
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => 'Failed to delete order',
                ], 500);
            }
    }
    public function searchOrderByUserId($user_id){

        $data = Order::with('good','good_para','user')
                                ->where('user_id',$user_id)
                                ->get();
        return $data;

    }

    function generateOrderInvoiceId($startChar = 'I') {
        // Generate a unique identifier (20 characters) using Laravel's Str::uuid() method.
        $uniqueId = Str::uuid();

        // Extract the last 6 digits from the unique identifier.
        $last6Digits = substr($uniqueId, -6);

        // Combine the starting character and 6-digit numerical sequence.
        $invoiceId = $startChar . '-' . $last6Digits;

        return $invoiceId;
    }

    public function searchOrderByShopId($shop_id)
    {
        $orders=Order::all();
        // $orders = DB::table('orders')
        //     ->rightJoin('carts', 'carts.user_id', '=', 'orders.user_id')
        //     ->rightJoin('goods', 'carts.good_id', '=', 'goods.id')
        //     ->rightJoin('shops', 'shops.id', '=', 'goods.shop_id')
        //     ->where('shops.id', '=', $shop_id)
        //     ->get();
        return $orders;
    }

    public function getOrderByMonth(Request $request)
    {
        $month = $request->month;
        $orders = Order::whereMonth('created_at', '=', $month)->get();
        $orders = count($orders);
        return response()->json(['data'=>$orders]);
    }

    public function getOrderList(Request $request)
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
                if (in_array($sortField, array('order_number', 'good_id', 'quantity'))) {
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
                $sortingName = 'order_number';
                $sortBy = 'asc';
            }

            $filterOptions = $request->gridState['filter']['filters'];
            if (count($filterOptions) > 0) {
                for ($i = 0; $i < count($filterOptions); $i++) {
                    $fieldName = $filterOptions[$i]['field'];
                    $fieldvalue = $filterOptions[$i]['value'];
                    $FilterBy = '';

                    if ($fieldName == 'order_number' || $fieldName == 'quantity') {
                        $fieldName = $fieldName;
                        $fieldvalue = '%' . $fieldvalue . '%';
                        $FilterBy = 'LIKE';
                    }
                    if ($fieldName == 'good_id') {
                        $fieldName = $fieldName;
                        $fieldvalue = $fieldvalue;
                        $FilterBy = '=';
                    }
                }
            }

            $qryResult = null;

            if (isset($fieldName)) {
                $qryResult = Order::where($fieldName, $FilterBy, $fieldvalue)
                    ->orderBy($sortingName, $sortBy)
                    ->skip($DisplayStart)
                    ->take($DisplayLength)
                    ->get();
            } else {
                $qryResult = Order::orderBy($sortingName, $sortBy)
                    ->skip($DisplayStart)
                    ->take($DisplayLength)
                    ->get();
            }

            $ordersWithCountryInfo = Order::all();
            $dataRowsCount = count($ordersWithCountryInfo);
            return response()->json(['data' => $qryResult, 'dataFoundRowsCount' => $dataRowsCount]);
    }
}
