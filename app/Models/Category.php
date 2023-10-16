<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'percentage',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function good() {
        return $this->hasMany(Good::class);
    }

}
