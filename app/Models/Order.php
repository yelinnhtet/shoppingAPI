<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table="orders";
    protected $fillable=['order_number', 'user_id','good_id','good_paras_id','good_specs_id','quantity','totalPrice'];
    
    protected $hidden=['created_at,updated_at'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function good_orders(){
        return $this->hasMany(Good_order::class);
    }

}
