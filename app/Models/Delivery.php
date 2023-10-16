<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table="deliveries";
    protected $fillable=['logo',
    'name'];
    protected $hidden = ['created_at', 'updated_at'];
}
