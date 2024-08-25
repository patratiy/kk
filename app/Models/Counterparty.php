<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counterparty extends Model
{
    use HasFactory;

    protected $table = 'counterparty';

    protected $fillable = [
        'ext_id',
        'name',
        'description',
        'status_id',
        'phone',
        'email',
        'inn',
        'actual_address',
        'company_type',
        'ext_code',
        'sales_amount',
        'tags',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'ext_id');
    }

    public function status()
    {
        return $this->hasOne(Product::class, 'status_id', 'product_id');
    }
}
