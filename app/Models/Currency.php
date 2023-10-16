<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $table="currencies";
    protected $fillable=['currency_name',
    'currency_symbol',
    'currency_code',
    'currency_exchange'];
    protected $hidden = ['created_at', 'updated_at'];


    public function withdraws(){
        return $this->hasMany(Withdraw::class);
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function good_paras()
    {
        return $this->hasMany(Good_para::class);
    }
}
