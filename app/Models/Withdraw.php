<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;
    protected $table="withdraws";
    protected $fillable=[
        'user_id',
        'amount',
        // 'accept_payment',
        'payment_id'
            ];
        // protected $hidden = ['created_at', 'updated_at'];


            public function user(){
                return $this->belongsTo(User::class);
            }

            public function currency(){
                return $this->belongsTo(Currency::class);
            }
            public function payment(){
                return $this->hasMany(Payment::class);
            }

}
