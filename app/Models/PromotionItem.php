<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionItem extends Model
{
    use HasFactory;

    protected $fillable = ['promotion_id','shop_id','good_id','update_price','update_qty'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
