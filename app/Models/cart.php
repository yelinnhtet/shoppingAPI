<?php

namespace App\Models;

use App\Models\Good;
use App\Models\User;
use App\Models\Good_para;
use App\Models\Good_spec;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class cart extends Model
{
    use HasFactory;
    protected $fillable=[
        'good_id','user_id','quantity','totalPrice','good_paras_id','good_specs_id'
    ];
    public function good_paras(){
        return $this->hasMany(Good_para::class,'id','good_paras_id');
    }
    public function good_specs(){
        return $this->hasMany(Good_spec::class,'id','good_specs_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function good(){
        return $this->belongsTo(Good::class,'good_id','id');
    }
}
