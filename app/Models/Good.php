<?php

namespace App\Models;
use App\Models\cart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;
    protected $fillable = ['name','photo','shop_id','price','discount','qty','description','category_id'];

    protected $hidden = ['created_at', 'updated_at'];

    // public function good_spec()
    // {
    //     return $this->belongsTo(Good_spec::class);
    // }

    // public function good_para()
    // {
    //     return $this->belongsTo(Good_para::class);
    // }

    public function GoodRating()
    {
        return $this->hasMany(GoodRating::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // public function order(){
    //     return $this->hasMany(Order::class);
    // }

    public function good_orders()
    {
        return $this->hasMany(Good_order::class);
    }
    public function cart(){
        return $this->hasMany(cart::class,'good_id','id');
    }

    public function good_specs()
    {
        return $this->hasMany(Good_spec::class);
    }

    public function good_paras()
    {
        return $this->hasMany(Good_para::class);
    }

    public function promotionItem()
    {
        return $this->hasOne(PromotionItem::class);
    }
}
