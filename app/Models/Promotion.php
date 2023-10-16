<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = "promotions";
    protected $fillable = ['title','image','start_date','end_date','type','status'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function promotionItems()
    {
        return $this->hasMany(PromotionItem::class);
    }
}
