<?php

namespace App\Models;

use App\Models\cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Good_spec extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
        'good_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function good()
    {
        return $this->belongsTo(Good::class);
    }
    public function cart()
    {
        return $this->belongsTo(cart::class);
    }
}
