<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;
    protected $table = "ads_six";
    protected $fillable = ['name','image_path'];
    protected $hidden = ['created_at', 'updated_at'];
}
