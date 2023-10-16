<?php

namespace App\Http\Controllers\Api;

use App\Models\cart;
use App\Models\Good;
use Dotenv\Util\Str;
use App\Models\Good_para;
use App\Models\Good_spec;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    public function cart(Request $req){
            cart::create([
                'user_id' => $req->userId,
                'good_id' => $req->id ,
                'quantity' => $req->total_quantity,
                'totalPrice' =>$req->total_price,
                'good_specs_id'=>$req->good_specs_id['id'],
                'good_paras_id'=>$req->good_paras_id['id']
            ]);

       return $req;
    }
    public function cartUpdate(Request $req,Response $id){
   return cart::find($req->id)->update([
        'user_id' => $req->userId,
        'good_id' => $req->good_id,
        'quantity' => $req->total_quantity,
        'totalPrice' =>$req->total_price,
        'good_paras_id'=>$req->good_paras[0]['id'],
        'good_specs_id'=>$req->good_specs[0]['id']
    ]);
    }
    public function index(){
        return Cache::remember('goods', 60*60*24 ,function () {
            $data=cart::with('user','good','good_paras','good_specs')->get();
        return $data;
        });
    }
    public function searchCartByUserId($user_id){
        $data = cart::with('user','good','good_paras','good_specs')
                                ->where('user_id',$user_id)
                                ->get();
        return $data;

    }
    public function delete($id){
        $cartDel=cart::find($id)->delete();
        return $cartDel;
    }
}
