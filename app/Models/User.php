<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\cart;
use App\Models\CartList;
use App\Models\ShopRating;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'name',
        'phone',
        'email',
        'password',
        'photo',
        'role',
        'agent_code',
        'status',
        'lat',
        'long',
        'phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function shop()
    {
        return $this->hasMany(Shop::class,'id');
    }
    public function shippingAddress()
    {
        return $this->hasMany(ShippingAddress::class);
    }
    public function ratingUser()
    {
        return $this->hasMany(RatingUser::class);
    }
    public function withdraws(){
        return $this->hasMany(Withdraw::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function goodRating(){
        return $this->hasMany(GoodRating::class);
    }

    public function shopRating(){
        return $this->hasMany(ShopRating::class);
    }

    public function order(){
        return $this->hasMany(Order::class);
    }
    public function cart(){
        return $this->hasMany(cart::class,'user_id','id');
    }
}
