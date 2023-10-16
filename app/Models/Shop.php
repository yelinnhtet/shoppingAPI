<?php

namespace App\Models;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['name','logo','cover_photo','user_id','shopkeeper_nrc','nrc','agent_user_id','lat','long','country_id','state_id','city_id','address','status','agent_code'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function countries()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function states()
    {
        return $this->belongsTo(State::class,'state_id','id');
    }
    public function cities()
    {
        return $this->belongsTo(City::class,'city_id','id');

    }

    public function ShopRating()
    {
        return $this->hasMany(ShopRating::class);
    }

    public function good()
    {
        return $this->hasMany(Good::class);
    }

    public function promotionItems()
    {
        return $this->hasMany(PromotionItem::class);
    }
}
