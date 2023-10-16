<?php

namespace App\Models;

use App\Models\Shop;
//use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'currency_id',
        'name',
        'phonecode',
        'lat',
        'long',  
        'country_img'      
    ];
    protected $hidden = ['created_at', 'updated_at'];


    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }
    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function shops()
    {
        return $this->hasMany(Shop::class,'id');
    }

    public function currency()
    {
        return $this->belongsTo(Country::class);
    }
}
