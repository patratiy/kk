<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressOrder extends Model
{
    use HasFactory;

    protected $table = 'address_order';

    protected $fillable = [
        'order_id',
        'full_address',
        'city',
        'region_id',
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'ext_id', 'order_id');
    }

    public function region()
    {
        return $this->hasOne(Region::class, 'ext_id', 'region_id');
    }
}
