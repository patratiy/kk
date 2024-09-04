<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    protected $table = 'basket';

    protected $fillable = [
        'order_id',
        'product_id',
        'count',
        'sale_price',
        'discount',
        'product_type',
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'ext_id', 'order_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'ext_id', 'product_id');
    }
}
