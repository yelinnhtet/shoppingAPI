<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact',
        'user_id',
        'country_id',
        'state_id',
        'city_id',
        'phone',
        'is_default',
        'address',     
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function user() {
        return $this->belongsToMany(User::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
}