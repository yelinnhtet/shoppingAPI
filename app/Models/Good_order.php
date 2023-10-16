<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good_order extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','good_id','good_para_id','price','quantity'];

    protected $hidden = ['created_at', 'updated_at'];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function good_para()
    {
        return $this->hasOne(Good_para::class);
    }


}
