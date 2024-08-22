<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'count',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'ext_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ext_id', 'product_id');
    }
}
