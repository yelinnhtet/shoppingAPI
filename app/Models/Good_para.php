<?php

namespace App\Models;

use App\Models\cart;
use App\Models\Good;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Good_para extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
        'currency_id',
        'good_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function good()
    {
        return $this->hasMany(Good::class);
    }
    // public function order(){
    //     return $this->hasMany(Order::class);
    // }
    public function currency(){
        return $this->belongsTo(Currency::class);
    }

    public function good_order(){
        return $this->belongsTo(Good_order::class);
    }
    public function cart()
    {
        return $this->belongsTo(cart::class);
    }
}
