<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopRating extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','shop_id','rating'];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shop() {
        return $this->belongsTo(Shop::class,'shop_id','id');
    }
    protected $hidden = ['created_at', 'updated_at'];



}
