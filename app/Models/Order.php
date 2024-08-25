<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'store_id',
        'status_id',
        'customer_id',
        'created_at',
        'updated_at',
        'ext_id',
        'order_number',
        'ext_code',
        'goods_count',
        'order_price',
    ];

    public function orderItems()
    {
        return $this->hasMany(Basket::class);
    }

    public function store()
    {
        return $this->hasOne(Stores::class, 'ext_id', 'store_id');
    }

    public function orderStatus()
    {
        return $this->hasOne(OrderStatus::class, 'ext_id', 'status_id');
    }

    public function customer()
    {
        return $this->hasOne(Counterparty::class, 'ext_id', 'customer_id');
    }
}
