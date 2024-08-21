<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'status_id',
        'customer_id',
        'created_at',
        'updated_at',
        'ext_id',
        'order_number',
        'ext_code',
        'code',
        'goods_count',
        'order_price',
    ];

    public function orderItems()
    {
        return $this->hasMany(Basket::class);
    }

    //@todo store
}
