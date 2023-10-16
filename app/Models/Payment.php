<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'logo',
        'name' ,
        'currency_id' 
    ];
    protected $hidden = ['created_at', 'updated_at'];
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    public function withdraw(){
        return $this->belongsTo(Withdraw::class);
    }
}
